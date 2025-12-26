<script>
    import Layout from '@/Shared/Layout.svelte';
    import { useForm } from '@inertiajs/svelte';
    import { inertia } from '@inertiajs/svelte';
    
    export let errors = {};
    
    const form = useForm({
        name: '',
        status: 'active',
        perform_whois: false,
        perform_dns: false,
    });
    
    function handleSubmit(e) {
        e.preventDefault();
        $form.post('/domains');
    }
</script>

<Layout title="Add New Domain">
    <div style="margin-bottom: 2rem;">
        <a href="/domains" use:inertia class="btn btn-secondary" style="text-decoration: none;">
            ← Back to Domains
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            Add New Domain
        </div>

        <form on:submit={handleSubmit}>
            <div class="form-group">
                <label for="name" class="form-label">Domain Name *</label>
                <input
                    type="text"
                    id="name"
                    bind:value={$form.name}
                    class="form-control"
                    class:is-invalid={errors.name}
                    placeholder="example.com"
                    required
                />
                {#if errors.name}
                    <div class="invalid-feedback">{errors.name}</div>
                {/if}
                <small style="color: #7f8c8d; font-size: 0.875rem;">
                    Enter a valid domain name (e.g., example.com, subdomain.example.org)
                </small>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select
                    id="status"
                    bind:value={$form.status}
                    class="form-control"
                >
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="pending">Pending</option>
                </select>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input
                        type="checkbox"
                        bind:checked={$form.perform_whois}
                        style="margin-right: 0.5rem;"
                    />
                    <span>Perform WHOIS lookup after adding domain</span>
                </label>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input
                        type="checkbox"
                        bind:checked={$form.perform_dns}
                        style="margin-right: 0.5rem;"
                    />
                    <span>Perform DNS lookup after adding domain</span>
                </label>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary" disabled={$form.processing}>
                    {$form.processing ? 'Adding...' : 'Add Domain'}
                </button>
                <a href="/domains" use:inertia class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</Layout>

<style>
    .is-invalid {
        border-color: #e74c3c !important;
    }
</style>
