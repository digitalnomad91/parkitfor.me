<script>
    import Layout from '@/Shared/Layout.svelte';
    import { inertia } from '@inertiajs/svelte';

    export let users;
</script>

<Layout title="Admin Panel">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h1 style="color: #2c3e50; margin-bottom: 0.25rem;">Admin Dashboard</h1>
            <p style="color: #7f8c8d; margin: 0;">Manage users and roles</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Users (page {users.current_page} of {users.last_page})
        </div>

        {#if users.data.length > 0}
        <div style="overflow-x: auto;">
            <table class="table-zebra">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    {#each users.data as user}
                    <tr>
                        <td>{user.id}</td>
                        <td>{user.name}</td>
                        <td>{user.email}</td>
                        <td>
                            {#if user.roles?.length}
                                <div class="role-list">
                                    {#each user.roles as role}
                                        <span class="role-pill">{role}</span>
                                    {/each}
                                </div>
                            {:else}
                                <span style="color: #7f8c8d;">None</span>
                            {/if}
                        </td>
                        <td>{new Date(user.created_at).toLocaleString()}</td>
                    </tr>
                    {/each}
                </tbody>
            </table>
        </div>

        {#if users.links?.length}
        <ul class="pagination">
            {#each users.links as link}
                <li class:active={link.active}>
                    {#if link.url}
                        <a href={link.url} use:inertia aria-label={link.label}>
                            {@html link.label}
                        </a>
                    {:else}
                        <span>{@html link.label}</span>
                    {/if}
                </li>
            {/each}
        </ul>
        {/if}
        {:else}
        <p style="text-align: center; padding: 2rem; color: #7f8c8d;">
            No users found yet.
        </p>
        {/if}
    </div>
</Layout>

<style>
    :global(.table-zebra tbody tr:nth-child(even)) {
        background-color: #f8f9fa;
    }

    :global(.table-zebra tbody tr:hover) {
        background-color: #f1f5f9;
    }

    :global(.pagination) {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 1.5rem;
        list-style: none;
        padding: 0;
    }

    :global(.pagination a),
    :global(.pagination span) {
        padding: 0.5rem 1rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        text-decoration: none;
        color: #333;
    }

    :global(.pagination .active span),
    :global(.pagination .active a) {
        background-color: #3498db;
        color: white;
        border-color: #3498db;
    }

    :global(.pagination a:hover) {
        background-color: #f8f9fa;
    }

    .role-list {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .role-pill {
        background-color: #e8f1ff;
        color: #2c3e50;
        padding: 0.35rem 0.75rem;
        border-radius: 999px;
        font-size: 0.875rem;
        font-weight: 600;
    }
</style>
