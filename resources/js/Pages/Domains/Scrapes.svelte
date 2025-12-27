<script>
    import Layout from '@/Shared/Layout.svelte';
    import { router, inertia } from '@inertiajs/svelte';
    
    export let domain;
    export let scrapes;
    export let sort = 'scraped_at';
    export let direction = 'desc';
    
    function performScrape() {
        router.post(`/domains/${domain.id}/scrape`);
    }
    
    function toggleSort(column) {
        const newDirection = (sort === column && direction === 'asc') ? 'desc' : 'asc';
        router.get(`/domains/${domain.id}/scrapes`, {
            sort: column,
            direction: newDirection
        }, {
            preserveState: true,
            preserveScroll: true
        });
    }
    
    function getSortIcon(column) {
        if (sort !== column) return '⇅';
        return direction === 'asc' ? '↑' : '↓';
    }
</script>

<Layout title="Scrapes - {domain.name}">
    <div style="margin-bottom: 2rem;">
        <a href="/domains" use:inertia class="btn btn-secondary" style="text-decoration: none;">
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
                        <th>
                            <button on:click={() => toggleSort('id')} style="background: none; border: none; cursor: pointer; color: inherit; font-weight: inherit;">
                                ID {getSortIcon('id')}
                            </button>
                        </th>
                        <th>
                            <button on:click={() => toggleSort('url')} style="background: none; border: none; cursor: pointer; color: inherit; font-weight: inherit;">
                                URL {getSortIcon('url')}
                            </button>
                        </th>
                        <th>
                            <button on:click={() => toggleSort('http_status_code')} style="background: none; border: none; cursor: pointer; color: inherit; font-weight: inherit;">
                                Status {getSortIcon('http_status_code')}
                            </button>
                        </th>
                        <th>
                            <button on:click={() => toggleSort('title')} style="background: none; border: none; cursor: pointer; color: inherit; font-weight: inherit;">
                                Title {getSortIcon('title')}
                            </button>
                        </th>
                        <th>
                            <button on:click={() => toggleSort('assets_count')} style="background: none; border: none; cursor: pointer; color: inherit; font-weight: inherit;">
                                Assets {getSortIcon('assets_count')}
                            </button>
                        </th>
                        <th>
                            <button on:click={() => toggleSort('links_count')} style="background: none; border: none; cursor: pointer; color: inherit; font-weight: inherit;">
                                Links {getSortIcon('links_count')}
                            </button>
                        </th>
                        <th>
                            <button on:click={() => toggleSort('scraped_at')} style="background: none; border: none; cursor: pointer; color: inherit; font-weight: inherit;">
                                Scraped At {getSortIcon('scraped_at')}
                            </button>
                        </th>
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
                                background-color: {scrape.http_status_code >= 200 && scrape.http_status_code < 300 ? '#d4edda' : '#f8d7da'};
                                color: {scrape.http_status_code >= 200 && scrape.http_status_code < 300 ? '#155724' : '#721c24'};
                            ">
                                {scrape.http_status_code}
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
        
        {#if scrapes.links}
            <div style="padding: 1rem; display: flex; justify-content: center; gap: 0.5rem;">
                {#each scrapes.links as link}
                    {#if link.url}
                        <a href={link.url} use:inertia 
                           class="btn {link.active ? 'btn-primary' : 'btn-secondary'}"
                           style="padding: 0.5rem 1rem; text-decoration: none;">
                            {@html link.label}
                        </a>
                    {:else}
                        <span style="padding: 0.5rem 1rem; color: #7f8c8d;">
                            {@html link.label}
                        </span>
                    {/if}
                {/each}
            </div>
        {/if}
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
