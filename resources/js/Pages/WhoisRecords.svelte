<script>
    import Layout from '@/Shared/Layout.svelte';
    
    export let whoisRecords;
</script>

<Layout title="WHOIS Records">
    <h1 style="margin-bottom: 2rem; color: #2c3e50;">WHOIS Records</h1>

    <div class="card">
        <div class="card-header">
            All WHOIS Records ({whoisRecords.total} total)
        </div>
        
        {#if whoisRecords.data.length > 0}
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Domain</th>
                        <th>Registrar</th>
                        <th>Name Servers</th>
                        <th>Creation Date</th>
                        <th>Expiration Date</th>
                        <th>Updated Date</th>
                        <th>Looked Up At</th>
                    </tr>
                </thead>
                <tbody>
                    {#each whoisRecords.data as record}
                    <tr>
                        <td>{record.id}</td>
                        <td><strong>{record.domain?.name || 'N/A'}</strong></td>
                        <td>{record.registrar || 'N/A'}</td>
                        <td>
                            {#if record.name_servers && record.name_servers.length > 0}
                                <div style="font-size: 0.875rem;">
                                    {record.name_servers.slice(0, 2).join(', ')}
                                    {#if record.name_servers.length > 2}
                                        <span style="color: #7f8c8d;">+{record.name_servers.length - 2} more</span>
                                    {/if}
                                </div>
                            {:else}
                                N/A
                            {/if}
                        </td>
                        <td>{record.creation_date ? new Date(record.creation_date).toLocaleDateString() : 'N/A'}</td>
                        <td>{record.expiration_date ? new Date(record.expiration_date).toLocaleDateString() : 'N/A'}</td>
                        <td>{record.updated_date ? new Date(record.updated_date).toLocaleDateString() : 'N/A'}</td>
                        <td>{new Date(record.created_at).toLocaleString()}</td>
                    </tr>
                    {/each}
                </tbody>
            </table>
        </div>
        {:else}
        <div style="text-align: center; padding: 3rem;">
            <p style="color: #7f8c8d; font-size: 1.1rem; margin-bottom: 1rem;">
                No WHOIS records found yet. Run the WHOIS lookup command to add records.
            </p>
            <p style="color: #7f8c8d; font-size: 0.9rem;">
                CLI:
                <br>
                <code style="background-color: #f8f9fa; padding: 0.5rem 1rem; border-radius: 4px; display: inline-block; margin-top: 0.5rem;">
                    php artisan whois:lookup example.com
                </code>
            </p>
        </div>
        {/if}
    </div>
</Layout>
