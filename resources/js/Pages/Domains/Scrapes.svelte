<script>
    import Layout from '@/Shared/Layout.svelte';
    import { router } from '@inertiajs/svelte';
    import { inertia } from '@inertiajs/svelte';
    
    export let domain;
    export let scrapes;
    
    function performScrape() {
        router.post(`/domains/${domain.id}/scrape`);
    }
</script>

<Layout title="Scrapes - {domain.name}">
    <div style="margin-bottom: 2rem;">
        <a href="/domains" class="btn btn-secondary" style="text-decoration: none;">
            ← Back to Domains
        </a>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="color: #2c3e50; margin: 0;">Website Scrapes for {domain.name}</h1>
        <button on:click={performScrape} class="btn btn-primary">
            🌐 Scrape Website
        </button>
    </div>

    <div class="card">
        <div class="card-header">
            Scrape History ({scrapes.total} total)
        </div>
        
        {#if scrapes.data.length > 0}
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>URL</th>
                        <th>Status</th>
                        <th>Title</th>
                        <th>Assets</th>
                        <th>Links</th>
                        <th>Scraped At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {#each scrapes.data as scrape}
                    <tr>
                        <td>{scrape.id}</td>
                        <td style="max-width: 200px; word-break: break-all;">
                            <a href={scrape.url} target="_blank" rel="noopener noreferrer" 
                               style="color: #3498db; text-decoration: none;">
                                {scrape.url}
                            </a>
                        </td>
                        <td>
                            <span style="
                                padding: 0.25rem 0.75rem;
                                border-radius: 12px;
                                font-size: 0.875rem;
                                background-color: {scrape.status_code >= 200 && scrape.status_code < 300 ? '#d4edda' : '#f8d7da'};
                                color: {scrape.status_code >= 200 && scrape.status_code < 300 ? '#155724' : '#721c24'};
                            ">
                                {scrape.status_code}
                            </span>
                        </td>
                        <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {scrape.title || 'N/A'}
                        </td>
                        <td>{scrape.assets_count || 0}</td>
                        <td>{scrape.links_count || 0}</td>
                        <td>{new Date(scrape.created_at).toLocaleString()}</td>
                        <td>
                            <a href="/domains/{domain.id}/scrapes/{scrape.id}" 
                               use:inertia
                               class="btn btn-primary" 
                               style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                                View Details
                            </a>
                        </td>
                    </tr>
                    {/each}
                </tbody>
            </table>
        </div>
        {:else}
        <div style="text-align: center; padding: 3rem;">
            <p style="color: #7f8c8d; font-size: 1.1rem; margin-bottom: 1.5rem;">
                No scrapes found for this domain yet.
            </p>
            <button on:click={performScrape} class="btn btn-primary">
                🌐 Scrape Website Now
            </button>
        </div>
        {/if}
    </div>
</Layout>
