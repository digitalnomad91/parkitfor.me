<script>
    import Layout from '@/Shared/Layout.svelte';
    import { inertia } from '@inertiajs/svelte';
    
    export let domains;
</script>

<Layout title="Domains">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="color: #2c3e50; margin: 0;">All Domains</h1>
        <a href="/domains/create" use:inertia class="btn btn-primary">
            ➕ Add New Domain
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            Domain List ({domains.total} total)
        </div>
        
        {#if domains.data.length > 0}
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Domain Name</th>
                        <th>TLD</th>
                        <th>Registrar</th>
                        <th>Status</th>
                        <th>Registered At</th>
                        <th>Expires At</th>
                        <th>WHOIS</th>
                        <th>DNS</th>
                        <th>Scrapes</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {#each domains.data as domain}
                    <tr>
                        <td>{domain.id}</td>
                        <td><strong>{domain.name}</strong></td>
                        <td>{domain.tld}</td>
                        <td>{domain.registrar || 'N/A'}</td>
                        <td>
                            <span style="
                                padding: 0.25rem 0.75rem;
                                border-radius: 12px;
                                font-size: 0.875rem;
                                background-color: {domain.status === 'active' ? '#d4edda' : '#f8d7da'};
                                color: {domain.status === 'active' ? '#155724' : '#721c24'};
                            ">
                                {domain.status.charAt(0).toUpperCase() + domain.status.slice(1)}
                            </span>
                        </td>
                        <td>{domain.registered_at ? new Date(domain.registered_at).toLocaleDateString() : 'N/A'}</td>
                        <td>{domain.expires_at ? new Date(domain.expires_at).toLocaleDateString() : 'N/A'}</td>
                        <td>{domain.whois_records_count}</td>
                        <td>{domain.dns_records_count || 0}</td>
                        <td>{domain.scrapes_count || 0}</td>
                        <td>{new Date(domain.created_at).toLocaleString()}</td>
                        <td style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <a href="/domains/{domain.id}/dns-records" 
                               use:inertia
                               class="btn btn-primary" 
                               style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                                📋 DNS
                            </a>
                            <a href="/domains/{domain.id}/scrapes" 
                               use:inertia
                               class="btn btn-secondary" 
                               style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                                🌐 Scrapes
                            </a>
                        </td>
                    </tr>
                    {/each}
                </tbody>
            </table>
        </div>
        {:else}
        <div style="text-align: center; padding: 3rem;">
            <p style="color: #7f8c8d; font-size: 1.1rem; margin-bottom: 1rem;">
                No domains found yet. Get started by adding your first domain!
            </p>
            <a href="/domains/create" use:inertia class="btn btn-primary">
                ➕ Add Your First Domain
            </a>
            <p style="color: #7f8c8d; margin-top: 1.5rem; font-size: 0.9rem;">
                Or use CLI:
                <br>
                <code style="background-color: #f8f9fa; padding: 0.5rem 1rem; border-radius: 4px; display: inline-block; margin-top: 0.5rem;">
                    php artisan domain:add example.com
                </code>
            </p>
        </div>
        {/if}
    </div>
</Layout>
