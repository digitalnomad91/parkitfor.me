<script>
    import Layout from '@/Shared/Layout.svelte';
    
    export let domain;
    export let scrape;
</script>

<Layout title="Scrape Detail - {domain.name}">
    <div style="margin-bottom: 2rem;">
        <a href="/domains/{domain.id}/scrapes" class="btn btn-secondary" style="text-decoration: none;">
            ← Back to Scrapes
        </a>
    </div>

    <h1 style="color: #2c3e50; margin-bottom: 2rem;">Scrape Details</h1>

    <div class="card">
        <div class="card-header">
            General Information
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <div>
                <strong style="color: #7f8c8d;">URL:</strong>
                <div style="margin-top: 0.5rem; word-break: break-all;">
                    <a href={scrape.url} target="_blank" rel="noopener noreferrer" 
                       style="color: #3498db; text-decoration: none;">
                        {scrape.url}
                    </a>
                </div>
            </div>
            
            <div>
                <strong style="color: #7f8c8d;">Status Code:</strong>
                <div style="margin-top: 0.5rem;">
                    <span style="
                        padding: 0.25rem 0.75rem;
                        border-radius: 12px;
                        font-size: 0.875rem;
                        background-color: {scrape.status_code >= 200 && scrape.status_code < 300 ? '#d4edda' : '#f8d7da'};
                        color: {scrape.status_code >= 200 && scrape.status_code < 300 ? '#155724' : '#721c24'};
                    ">
                        {scrape.status_code}
                    </span>
                </div>
            </div>
            
            <div>
                <strong style="color: #7f8c8d;">Title:</strong>
                <div style="margin-top: 0.5rem;">{scrape.title || 'N/A'}</div>
            </div>
            
            <div>
                <strong style="color: #7f8c8d;">Scraped At:</strong>
                <div style="margin-top: 0.5rem;">{new Date(scrape.created_at).toLocaleString()}</div>
            </div>
        </div>
        
        {#if scrape.description}
        <div style="margin-top: 1.5rem;">
            <strong style="color: #7f8c8d;">Meta Description:</strong>
            <div style="margin-top: 0.5rem; padding: 1rem; background-color: #f8f9fa; border-radius: 4px;">
                {scrape.description}
            </div>
        </div>
        {/if}
    </div>

    <div class="card">
        <div class="card-header">
            Assets ({scrape.assets?.length || 0} total)
        </div>
        
        {#if scrape.assets && scrape.assets.length > 0}
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>URL</th>
                        <th>Found At</th>
                    </tr>
                </thead>
                <tbody>
                    {#each scrape.assets as asset}
                    <tr>
                        <td>
                            <span style="
                                padding: 0.25rem 0.5rem;
                                border-radius: 4px;
                                font-size: 0.875rem;
                                font-weight: 600;
                                background-color: #e8f5e9;
                                color: #2e7d32;
                            ">
                                {asset.type}
                            </span>
                        </td>
                        <td style="word-break: break-all; max-width: 500px;">
                            <a href={asset.url} target="_blank" rel="noopener noreferrer" 
                               style="color: #3498db; text-decoration: none;">
                                {asset.url}
                            </a>
                        </td>
                        <td>{new Date(asset.created_at).toLocaleString()}</td>
                    </tr>
                    {/each}
                </tbody>
            </table>
        </div>
        {:else}
        <p style="text-align: center; padding: 2rem; color: #7f8c8d;">
            No assets found in this scrape.
        </p>
        {/if}
    </div>

    <div class="card">
        <div class="card-header">
            Links ({scrape.links?.length || 0} total)
        </div>
        
        {#if scrape.links && scrape.links.length > 0}
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>URL</th>
                        <th>Anchor Text</th>
                        <th>Is External</th>
                        <th>Found At</th>
                    </tr>
                </thead>
                <tbody>
                    {#each scrape.links as link}
                    <tr>
                        <td style="word-break: break-all; max-width: 400px;">
                            <a href={link.url} target="_blank" rel="noopener noreferrer" 
                               style="color: #3498db; text-decoration: none;">
                                {link.url}
                            </a>
                        </td>
                        <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {link.anchor_text || 'N/A'}
                        </td>
                        <td>
                            {#if link.is_external}
                                <span style="color: #e67e22;">External</span>
                            {:else}
                                <span style="color: #27ae60;">Internal</span>
                            {/if}
                        </td>
                        <td>{new Date(link.created_at).toLocaleString()}</td>
                    </tr>
                    {/each}
                </tbody>
            </table>
        </div>
        {:else}
        <p style="text-align: center; padding: 2rem; color: #7f8c8d;">
            No links found in this scrape.
        </p>
        {/if}
    </div>
</Layout>
