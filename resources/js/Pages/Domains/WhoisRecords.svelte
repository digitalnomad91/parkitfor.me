<script>
    import Layout from '@/Shared/Layout.svelte';
    import { inertia, router } from '@inertiajs/svelte';
    
    export let domain;
    export let whoisRecords;
    export let sort;
    export let direction;
    
    function toggleSort(column) {
        const newDirection = (sort === column && direction === 'asc') ? 'desc' : 'asc';
        router.get(`/domains/${domain.id}/whois-records`, {
            sort: column,
            direction: newDirection
        }, {
            preserveState: true,
            preserveScroll: true
        });
    }
    
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        return new Date(dateString).toLocaleDateString();
    }
    
    function formatDateTime(dateString) {
        if (!dateString) return 'N/A';
        return new Date(dateString).toLocaleString();
    }
    
    function getSortIcon(column) {
        if (sort !== column) return '⇅';
        return direction === 'asc' ? '↑' : '↓';
    }
    
    function showJsonModal(record) {
        alert(JSON.stringify(record, null, 2));
    }
</script>

<Layout title="WHOIS History: {domain.name}">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <p style="color: #7f8c8d; margin-bottom: 0.35rem;">WHOIS History</p>
            <h1 style="color: #2c3e50; margin: 0;">{domain.name}</h1>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="/domains/{domain.id}" use:inertia class="btn btn-secondary">← Domain Details</a>
            <a href="/domains" use:inertia class="btn btn-secondary">All Domains</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <span>WHOIS Records for {domain.name}</span>
            <small style="color: #7f8c8d;">Click any row to open the raw JSON payload</small>
        </div>

        {#if whoisRecords.data && whoisRecords.data.length > 0}
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
                                <button on:click={() => toggleSort('registrar')} style="background: none; border: none; cursor: pointer; color: inherit; font-weight: inherit;">
                                    Registrar {getSortIcon('registrar')}
                                </button>
                            </th>
                            <th>
                                <button on:click={() => toggleSort('creation_date')} style="background: none; border: none; cursor: pointer; color: inherit; font-weight: inherit;">
                                    Created {getSortIcon('creation_date')}
                                </button>
                            </th>
                            <th>
                                <button on:click={() => toggleSort('expiration_date')} style="background: none; border: none; cursor: pointer; color: inherit; font-weight: inherit;">
                                    Expires {getSortIcon('expiration_date')}
                                </button>
                            </th>
                            <th>
                                <button on:click={() => toggleSort('updated_date')} style="background: none; border: none; cursor: pointer; color: inherit; font-weight: inherit;">
                                    Updated {getSortIcon('updated_date')}
                                </button>
                            </th>
                            <th>
                                <button on:click={() => toggleSort('status')} style="background: none; border: none; cursor: pointer; color: inherit; font-weight: inherit;">
                                    Status {getSortIcon('status')}
                                </button>
                            </th>
                            <th>Nameservers</th>
                            <th>
                                <button on:click={() => toggleSort('created_at')} style="background: none; border: none; cursor: pointer; color: inherit; font-weight: inherit;">
                                    Looked Up At {getSortIcon('created_at')}
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {#each whoisRecords.data as record}
                            <tr style="cursor: pointer;" on:click={() => showJsonModal(record)}>
                                <td>{record.id}</td>
                                <td>{record.registrar || 'N/A'}</td>
                                <td>{formatDate(record.creation_date)}</td>
                                <td>{formatDate(record.expiration_date)}</td>
                                <td>{formatDate(record.updated_date)}</td>
                                <td>{record.status || 'N/A'}</td>
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

            {#if whoisRecords.links}
                <div style="padding: 1rem; display: flex; justify-content: center; gap: 0.5rem;">
                    {#each whoisRecords.links as link}
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
            <p style="color: #7f8c8d; padding: 1rem;">No WHOIS records captured for this domain yet.</p>
        {/if}
    </div>
</Layout>
