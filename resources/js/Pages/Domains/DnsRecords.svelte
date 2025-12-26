<script>
    import Layout from '@/Shared/Layout.svelte';
    import { router } from '@inertiajs/svelte';
    
    export let domain;
    export let dnsRecords;
    
    function performLookup() {
        router.post(`/domains/${domain.id}/dns-lookup`);
    }
</script>

<Layout title="DNS Records - {domain.name}">
    <div style="margin-bottom: 2rem;">
        <a href="/domains" class="btn btn-secondary" style="text-decoration: none;">
            ← Back to Domains
        </a>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="color: #2c3e50; margin: 0;">DNS Records for {domain.name}</h1>
        <button on:click={performLookup} class="btn btn-primary">
            🔄 Refresh DNS Records
        </button>
    </div>

    <div class="card">
        <div class="card-header">
            DNS Records ({dnsRecords.total} total)
        </div>
        
        {#if dnsRecords.data.length > 0}
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>TTL</th>
                        <th>Priority</th>
                        <th>Looked Up At</th>
                    </tr>
                </thead>
                <tbody>
                    {#each dnsRecords.data as record}
                    <tr>
                        <td>{record.id}</td>
                        <td>
                            <span style="
                                padding: 0.25rem 0.5rem;
                                border-radius: 4px;
                                font-size: 0.875rem;
                                font-weight: 600;
                                background-color: #e3f2fd;
                                color: #1976d2;
                            ">
                                {record.type}
                            </span>
                        </td>
                        <td style="word-break: break-all; max-width: 400px;">{record.value}</td>
                        <td>{record.ttl || 'N/A'}</td>
                        <td>{record.priority || 'N/A'}</td>
                        <td>{new Date(record.created_at).toLocaleString()}</td>
                    </tr>
                    {/each}
                </tbody>
            </table>
        </div>
        {:else}
        <div style="text-align: center; padding: 3rem;">
            <p style="color: #7f8c8d; font-size: 1.1rem; margin-bottom: 1.5rem;">
                No DNS records found for this domain yet.
            </p>
            <button on:click={performLookup} class="btn btn-primary">
                🔄 Perform DNS Lookup
            </button>
        </div>
        {/if}
    </div>
</Layout>
