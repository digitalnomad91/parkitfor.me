<?php

namespace App\Services;

use App\Models\Domain;
use App\Models\Scrape;
use App\Models\ScrapeAsset;
use App\Models\ScrapeLink;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;
use Exception;

class WebScraperService
{
    protected ?RemoteWebDriver $driver = null;
    protected string $seleniumHost = 'http://localhost:4444';
    protected int $timeout = 30;

    public function __construct()
    {
        // Check if we have Playwright available (from existing browser tool)
        // If not, we'll use headless Chrome/Selenium
    }

    /**
     * Scrape a domain
     */
    public function scrapeDomain(Domain $domain, ?string $url = null): Scrape
    {
        $url = $url ?? 'https://' . $domain->name;
        
        $scrape = Scrape::create([
            'domain_id' => $domain->id,
            'url' => $url,
            'status' => 'processing',
        ]);

        try {
            $startTime = microtime(true);
            
            // Use cURL for initial request and basic info
            $htmlContent = $this->fetchHtmlWithCurl($url, $scrape);
            
            $responseTime = (int)((microtime(true) - $startTime) * 1000);
            $scrape->response_time_ms = $responseTime;
            
            if ($htmlContent) {
                // Parse HTML content
                $this->parseHtmlContent($htmlContent, $scrape, $url);
                
                // Extract and download assets
                $this->extractAndDownloadAssets($htmlContent, $scrape, $url);
                
                // Extract links
                $this->extractLinks($htmlContent, $scrape, $url);
                
                // Try to take screenshot using available browser automation
                $this->captureScreenshotAndVideo($url, $scrape);
            }
            
            $scrape->update([
                'status' => 'completed',
                'scraped_at' => now(),
            ]);
            
        } catch (Exception $e) {
            $scrape->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            
            throw $e;
        }

        return $scrape->fresh();
    }

    /**
     * Fetch HTML using cURL with headers
     */
    protected function fetchHtmlWithCurl(string $url, Scrape $scrape): ?string
    {
        $ch = curl_init($url);
        
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            CURLOPT_HEADER => true,
        ]);
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);
        
        curl_close($ch);
        
        if ($error) {
            throw new Exception("cURL error: {$error}");
        }
        
        // Parse headers and body
        $headerSize = $info['header_size'];
        $headers = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        
        $scrape->update([
            'http_status_code' => $info['http_code'],
            'http_headers' => $headers,
            'content_type' => $info['content_type'] ?? null,
            'content_length' => $info['download_content_length'] ?? strlen($body),
        ]);
        
        return $body;
    }

    /**
     * Parse HTML content and extract metadata
     */
    protected function parseHtmlContent(string $html, Scrape $scrape, string $baseUrl): void
    {
        $crawler = new Crawler($html, $baseUrl);
        
        // Extract title
        $title = null;
        try {
            $title = $crawler->filter('title')->count() > 0 
                ? $crawler->filter('title')->text() 
                : null;
        } catch (Exception $e) {
            // Title extraction failed
        }
        
        // Extract meta description
        $metaDescription = null;
        try {
            $metaNode = $crawler->filter('meta[name="description"]');
            if ($metaNode->count() > 0) {
                $metaDescription = $metaNode->attr('content');
            }
        } catch (Exception $e) {
            // Meta description extraction failed
        }
        
        // Extract all meta tags
        $metaTags = [];
        try {
            $crawler->filter('meta')->each(function (Crawler $node) use (&$metaTags) {
                $name = $node->attr('name') ?? $node->attr('property') ?? null;
                $content = $node->attr('content');
                if ($name && $content) {
                    $metaTags[$name] = $content;
                }
            });
        } catch (Exception $e) {
            // Meta tags extraction failed
        }
        
        // Extract Open Graph data
        $openGraphData = [];
        try {
            $crawler->filter('meta[property^="og:"]')->each(function (Crawler $node) use (&$openGraphData) {
                $property = str_replace('og:', '', $node->attr('property'));
                $openGraphData[$property] = $node->attr('content');
            });
        } catch (Exception $e) {
            // OG data extraction failed
        }
        
        // Extract favicon
        $faviconUrl = null;
        try {
            $faviconNode = $crawler->filter('link[rel*="icon"]');
            if ($faviconNode->count() > 0) {
                $faviconUrl = $faviconNode->first()->attr('href');
                $faviconUrl = $this->resolveUrl($faviconUrl, $baseUrl);
                
                // Download favicon
                $faviconPath = $this->downloadAsset($faviconUrl, 'image');
                if ($faviconPath) {
                    $scrape->update(['favicon_path' => 'storage/' . $faviconPath]);
                }
            }
        } catch (Exception $e) {
            // Favicon extraction failed
        }
        
        $scrape->update([
            'html_body' => $html,
            'title' => $title,
            'meta_description' => $metaDescription,
            'meta_tags' => $metaTags,
            'open_graph_data' => $openGraphData,
        ]);
    }

    /**
     * Extract and download assets from HTML
     */
    protected function extractAndDownloadAssets(string $html, Scrape $scrape, string $baseUrl): void
    {
        $crawler = new Crawler($html, $baseUrl);
        $assetMappings = [
            'link[rel="stylesheet"]' => ['attr' => 'href', 'type' => 'css'],
            'script[src]' => ['attr' => 'src', 'type' => 'js'],
            'img[src]' => ['attr' => 'src', 'type' => 'image'],
            'source[src]' => ['attr' => 'src', 'type' => 'media'],
            'video[src]' => ['attr' => 'src', 'type' => 'video'],
            'audio[src]' => ['attr' => 'src', 'type' => 'audio'],
        ];
        
        foreach ($assetMappings as $selector => $config) {
            try {
                $crawler->filter($selector)->each(function (Crawler $node) use ($config, $scrape, $baseUrl) {
                    $assetUrl = $node->attr($config['attr']);
                    if (!$assetUrl || str_starts_with($assetUrl, 'data:')) {
                        return;
                    }
                    
                    $assetUrl = $this->resolveUrl($assetUrl, $baseUrl);
                    
                    // Find or create asset
                    $asset = ScrapeAsset::firstOrCreate(
                        ['url' => $assetUrl],
                        ['type' => $config['type'], 'status' => 'pending']
                    );
                    
                    // Download if not already downloaded
                    if ($asset->status !== 'downloaded') {
                        $this->downloadAssetToModel($asset);
                    }
                    
                    // Associate with scrape
                    if (!$scrape->assets()->where('scrape_asset_id', $asset->id)->exists()) {
                        $scrape->assets()->attach($asset->id);
                    }
                });
            } catch (Exception $e) {
                // Asset extraction failed for this selector
            }
        }
    }

    /**
     * Extract links from HTML
     */
    protected function extractLinks(string $html, Scrape $scrape, string $baseUrl): void
    {
        $crawler = new Crawler($html, $baseUrl);
        $position = 0;
        
        try {
            $crawler->filter('a[href]')->each(function (Crawler $node) use ($scrape, $baseUrl, &$position) {
                $position++;
                $href = $node->attr('href');
                
                if (!$href || str_starts_with($href, '#')) {
                    return;
                }
                
                $fullUrl = $this->resolveUrl($href, $baseUrl);
                $anchorText = trim($node->text());
                $rel = $node->attr('rel');
                $target = $node->attr('target');
                
                // Determine link type
                $linkType = $this->determineLinkType($fullUrl, $baseUrl);
                $isNofollow = $rel && str_contains(strtolower($rel), 'nofollow');
                
                ScrapeLink::create([
                    'scrape_id' => $scrape->id,
                    'url' => $fullUrl,
                    'anchor_text' => Str::limit($anchorText, 255),
                    'rel' => $rel,
                    'target' => $target,
                    'link_type' => $linkType,
                    'is_nofollow' => $isNofollow,
                    'position' => $position,
                ]);
            });
        } catch (Exception $e) {
            // Link extraction failed
        }
    }

    /**
     * Capture screenshot and video using available browser automation
     */
    protected function captureScreenshotAndVideo(string $url, Scrape $scrape): void
    {
        // This would integrate with Selenium/ChromeDriver or use existing Playwright from the system
        // For now, we'll implement a basic version that can be extended
        
        try {
            // Try to use ChromeDriver if available
            $screenshotPath = $this->captureWithChrome($url);
            if ($screenshotPath) {
                $scrape->update(['screenshot_path' => $screenshotPath]);
            }
        } catch (Exception $e) {
            // Screenshot capture failed - not critical
        }
    }

    /**
     * Capture screenshot with Chrome/Selenium
     */
    protected function captureWithChrome(string $url): ?string
    {
        try {
            $options = new ChromeOptions();
            $options->addArguments([
                '--headless',
                '--disable-gpu',
                '--no-sandbox',
                '--disable-dev-shm-usage',
                '--window-size=1920,1080',
            ]);
            
            $capabilities = DesiredCapabilities::chrome();
            $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
            
            $this->driver = RemoteWebDriver::create($this->seleniumHost, $capabilities, 60000, 60000);
            $this->driver->get($url);
            
            // Wait for page to load
            sleep(3);
            
            // Take screenshot
            $screenshot = $this->driver->takeScreenshot();
            
            // Save screenshot
            $filename = 'scrapes/screenshots/' . Str::uuid() . '.png';
            Storage::disk('public')->put($filename, $screenshot);
            
            $this->driver->quit();
            $this->driver = null;
            
            return 'storage/' . $filename;
            
        } catch (Exception $e) {
            if ($this->driver) {
                try {
                    $this->driver->quit();
                } catch (Exception $ex) {
                    // Ignore
                }
                $this->driver = null;
            }
            return null;
        }
    }

    /**
     * Download asset to model
     */
    protected function downloadAssetToModel(ScrapeAsset $asset): void
    {
        try {
            $asset->download_attempts++;
            $asset->save();
            
            $filePath = $this->downloadAsset($asset->url, $asset->type);
            
            if ($filePath) {
                $fileSize = Storage::disk('public')->size($filePath);
                $mimeType = Storage::disk('public')->mimeType($filePath);
                $fileContent = Storage::disk('public')->get($filePath);
                $hash = hash('sha256', $fileContent);
                
                $asset->update([
                    'file_path' => 'storage/' . $filePath,
                    'file_size' => $fileSize,
                    'mime_type' => $mimeType,
                    'hash' => $hash,
                    'status' => 'downloaded',
                    'downloaded_at' => now(),
                ]);
            } else {
                $asset->update(['status' => 'failed']);
            }
        } catch (Exception $e) {
            $asset->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Download asset file
     */
    protected function downloadAsset(string $url, string $type): ?string
    {
        try {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 3,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ]);
            
            $content = curl_exec($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);
            
            if ($content && $info['http_code'] === 200) {
                $extension = $this->getExtensionFromUrl($url) ?? $this->getExtensionFromType($type);
                $filename = 'scrapes/assets/' . $type . '/' . Str::uuid() . '.' . $extension;
                
                Storage::disk('public')->put($filename, $content);
                
                return $filename;
            }
            
            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Resolve relative URL to absolute
     */
    protected function resolveUrl(string $url, string $baseUrl): string
    {
        if (parse_url($url, PHP_URL_SCHEME) !== null) {
            return $url;
        }
        
        $base = parse_url($baseUrl);
        
        if (str_starts_with($url, '//')) {
            return ($base['scheme'] ?? 'https') . ':' . $url;
        }
        
        if (str_starts_with($url, '/')) {
            return ($base['scheme'] ?? 'https') . '://' . $base['host'] . $url;
        }
        
        $path = $base['path'] ?? '/';
        $path = rtrim(dirname($path), '/');
        
        return ($base['scheme'] ?? 'https') . '://' . $base['host'] . $path . '/' . $url;
    }

    /**
     * Determine link type
     */
    protected function determineLinkType(string $url, string $baseUrl): string
    {
        if (str_starts_with($url, 'mailto:')) {
            return 'mailto';
        }
        
        if (str_starts_with($url, 'tel:')) {
            return 'tel';
        }
        
        if (str_starts_with($url, 'javascript:')) {
            return 'javascript';
        }
        
        $urlHost = parse_url($url, PHP_URL_HOST);
        $baseHost = parse_url($baseUrl, PHP_URL_HOST);
        
        if ($urlHost === $baseHost) {
            return 'internal';
        }
        
        return 'external';
    }

    /**
     * Get file extension from URL
     */
    protected function getExtensionFromUrl(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        return $extension ?: null;
    }

    /**
     * Get default extension from type
     */
    protected function getExtensionFromType(string $type): string
    {
        return match($type) {
            'css' => 'css',
            'js' => 'js',
            'image' => 'jpg',
            'video' => 'mp4',
            'audio' => 'mp3',
            'font' => 'woff2',
            default => 'bin',
        };
    }
}
