<script>
    import Layout from '@/Shared/Layout.svelte';
    
    export let domains;
    export let whoisRecords;
</script>

<Layout title="Dashboard">
    <h1 style="margin-bottom: 2rem; color: #2c3e50;">Dashboard</h1>

    <div class="grid">
        <div class="stat-card">
            <h3>Total Domains</h3>
            <div class="stat-value">{domains.total}</div>
        </div>
        
        <div class="stat-card">
            <h3>Total WHOIS Records</h3>
            <div class="stat-value">{whoisRecords.total}</div>
        </div>
        
        <div class="stat-card">
            <h3>Recent Lookups</h3>
            <div class="stat-value">{whoisRecords.data.length}</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Recent Domains
        </div>
        
        {#if domains.data.length > 0}
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Domain Name</th>
                    <th>TLD</th>
                    <th>Status</th>
                    <th>WHOIS Records</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                {#each domains.data as domain}
                <tr>
                    <td>{domain.id}</td>
                    <td>{domain.name}</td>
                    <td>{domain.tld}</td>
                    <td>{domain.status}</td>
                    <td>{domain.whois_records_count}</td>
                    <td>{new Date(domain.created_at).toLocaleString()}</td>
                </tr>
                {/each}
            </tbody>
        </table>
        {:else}
        <p style="text-align: center; padding: 2rem; color: #7f8c8d;">
            No domains found. Run the WHOIS lookup command to add domains.
        </p>
        {/if}
    </div>

    <div class="card">
        <div class="card-header">
            Recent WHOIS Lookups
        </div>
        
        {#if whoisRecords.data.length > 0}
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Domain</th>
                    <th>Registrar</th>
                    <th>Creation Date</th>
                    <th>Expiration Date</th>
                    <th>Looked Up At</th>
                </tr>
            </thead>
            <tbody>
                {#each whoisRecords.data as record}
                <tr>
                    <td>{record.id}</td>
                    <td>{record.domain?.name || 'N/A'}</td>
                    <td>{record.registrar || 'N/A'}</td>
                    <td>{record.creation_date ? new Date(record.creation_date).toLocaleDateString() : 'N/A'}</td>
                    <td>{record.expiration_date ? new Date(record.expiration_date).toLocaleDateString() : 'N/A'}</td>
                    <td>{new Date(record.created_at).toLocaleString()}</td>
                </tr>
                {/each}
            </tbody>
        </table>
        {:else}
        <p style="text-align: center; padding: 2rem; color: #7f8c8d;">
            No WHOIS records found. Run the WHOIS lookup command to add records.
        </p>
        {/if}
    </div>
</Layout>
