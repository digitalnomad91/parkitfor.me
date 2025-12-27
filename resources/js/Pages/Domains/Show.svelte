<script>
    import Layout from '@/Shared/Layout.svelte';
    import { router } from '@inertiajs/svelte';
    import { inertia } from '@inertiajs/svelte';
    
    export let domain;
    export let latestWhois;
    export let recentWhoisRecords;
    export let dnsRecords;
    export let recentScrapes;
    
    function performDnsLookup() {
        router.post(`/domains/${domain.id}/dns-lookup`);
    }
    
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        return new Date(dateString).toLocaleDateString();
    }
    
    function formatDateTime(dateString) {
        if (!dateString) return 'N/A';
        return new Date(dateString).toLocaleString();
    }
    
    function getRelativeTime(dateString) {
        if (!dateString) return 'Never';
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        const seconds = Math.floor(diff / 1000);
        const minutes = Math.floor(seconds / 60);
        const hours = Math.floor(minutes / 60);
        const days = Math.floor(hours / 24);
        
        if (days > 0) return `${days} day${days > 1 ? 's' : ''} ago`;
        if (hours > 0) return `${hours} hour${hours > 1 ? 's' : ''} ago`;
        if (minutes > 0) return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
        return 'Just now';
    }
</script>

<Layout title="Domain: {domain.name}">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <p style="color: #7f8c8d; margin-bottom: 0.35rem;">Domain Details</p>
            <h1 style="color: #2c3e50; margin: 0;">{domain.name}</h1>
        </div>
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <button on:click={performDnsLookup} class="btn btn-secondary">🔄 Refresh DNS</button>
            <a href="/domains" use:inertia class="btn btn-secondary">← Back to Domains</a>
        </div>
    </div>

    <div class="grid" style="margin-bottom: 1.5rem;">
        <div class="stat-card">
            <h3>Status</h3>
            <div class="stat-value" style="font-size: 1.6rem;">
                {domain.status ? domain.status.charAt(0).toUpperCase() + domain.status.slice(1) : 'Unknown'}
            </div>
            <p style="color: #7f8c8d; margin-top: 0.5rem;">
                Registrar: {domain.registrar || (latestWhois?.registrar) || 'N/A'}
            </p>
        </div>
        <div class="stat-card">
            <h3>WHOIS Records</h3>
            <div class="stat-value">{domain.whois_records_count}</div>
            <p style="color: #7f8c8d; margin-top: 0.5rem;">
                Last lookup: {latestWhois ? getRelativeTime(latestWhois.created_at) : 'Never'}
            </p>
        </div>
        <div class="stat-card">
            <h3>DNS Records</h3>
            <div class="stat-value">{domain.dns_records_count}</div>
            <p style="color: #7f8c8d; margin-top: 0.5rem;">
                Unique types: {dnsRecords ? new Set(dnsRecords.map(r => r.record_type)).size : 0}
            </p>
        </div>
        <div class="stat-card">
            <h3>Scrapes</h3>
            <div class="stat-value">{domain.scrapes_count}</div>
            <p style="color: #7f8c8d; margin-top: 0.5rem;">
                Latest scrape: {recentScrapes && recentScrapes.length > 0 ? getRelativeTime(recentScrapes[0].scraped_at) : 'Never'}
            </p>
        </div>
    </div>

    <div class="card" id="whois-section" style="margin-bottom: 1.5rem;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <span>Current WHOIS Snapshot</span>
            <a href="/domains/{domain.id}/whois-records" use:inertia class="btn btn-primary" style="padding: 0.5rem 1rem;">
                View All WHOIS
            </a>
        </div>

        {#if latestWhois}
            <div class="grid" style="margin-bottom: 1rem;">
                <div class="stat-card" style="border-left-color: #2ecc71;">
                    <h3>Registrar</h3>
                    <div class="stat-value" style="font-size: 1.25rem;">{latestWhois.registrar || 'N/A'}</div>
                    <p style="color: #7f8c8d; margin-top: 0.4rem;">Status: {latestWhois.status || 'N/A'}</p>
                </div>
                <div class="stat-card" style="border-left-color: #3498db;">
                    <h3>Created</h3>
                    <div class="stat-value" style="font-size: 1.25rem;">{formatDate(latestWhois.creation_date)}</div>
                    <p style="color: #7f8c8d; margin-top: 0.4rem;">Updated: {formatDate(latestWhois.updated_date)}</p>
                </div>
                <div class="stat-card" style="border-left-color: #e67e22;">
                    <h3>Expiration</h3>
                    <div class="stat-value" style="font-size: 1.25rem;">{formatDate(latestWhois.expiration_date)}</div>
                    <p style="color: #7f8c8d; margin-top: 0.4rem;">
                        Nameservers: {latestWhois.nameservers ? (Array.isArray(latestWhois.nameservers) ? latestWhois.nameservers.slice(0, 3).join(', ') : 'N/A') : 'N/A'}
                    </p>
                </div>
            </div>
        {:else}
            <p style="color: #7f8c8d; padding: 1rem;">No WHOIS data yet for this domain. Run a lookup to populate the snapshot.</p>
        {/if}
    </div>

    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <span>Recent WHOIS Records (latest 5)</span>
            <small style="color: #7f8c8d;">Click a row to view the raw JSON</small>
        </div>

        {#if recentWhoisRecords && recentWhoisRecords.length > 0}
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Registrar</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Expires</th>
                            <th>Nameservers</th>
                            <th>Looked Up At</th>
                        </tr>
                    </thead>
                    <tbody>
                        {#each recentWhoisRecords as record}
                            <tr style="cursor: pointer;" 
                                on:click={() => alert(JSON.stringify(record, null, 2))}>
                                <td>{record.id}</td>
                                <td>{record.registrar || 'N/A'}</td>
                                <td>{record.status || 'N/A'}</td>
                                <td>{formatDate(record.creation_date)}</td>
                                <td>{formatDate(record.expiration_date)}</td>
                                <td>
                                    {#if record.nameservers && Array.isArray(record.nameservers)}
                                        <small>{record.nameservers.slice(0, 3).join(', ')}</small>
                                    {:else}
                                        N/A
                                    {/if}
                                </td>
                                <td>{formatDateTime(record.created_at)}</td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>
        {:else}
            <p style="color: #7f8c8d; padding: 1rem;">No WHOIS records found yet.</p>
        {/if}
    </div>
</Layout>
