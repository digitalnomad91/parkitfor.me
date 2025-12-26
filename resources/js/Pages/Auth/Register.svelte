<script>
    import { useForm } from '@inertiajs/svelte';
    
    export let errors = {};
    
    const form = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });
    
    function handleSubmit(e) {
        e.preventDefault();
        $form.post('/register');
    }
</script>

<svelte:head>
    <title>Register - {import.meta.env.VITE_APP_NAME || 'Laravel'}</title>
</svelte:head>

<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background-color: #f5f5f5; padding: 1rem;">
    <div class="card" style="max-width: 450px; width: 100%;">
        <div class="card-header">
            Register
        </div>

        <form on:submit={handleSubmit}>
            <div class="form-group">
                <label for="name" class="form-label">Name</label>
                <input
                    type="text"
                    id="name"
                    bind:value={$form.name}
                    class="form-control"
                    class:is-invalid={errors.name}
                    placeholder="John Doe"
                    required
                />
                {#if errors.name}
                    <div class="invalid-feedback">{errors.name}</div>
                {/if}
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input
                    type="email"
                    id="email"
                    bind:value={$form.email}
                    class="form-control"
                    class:is-invalid={errors.email}
                    placeholder="your@email.com"
                    required
                />
                {#if errors.email}
                    <div class="invalid-feedback">{errors.email}</div>
                {/if}
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    id="password"
                    bind:value={$form.password}
                    class="form-control"
                    class:is-invalid={errors.password}
                    placeholder="••••••••"
                    required
                />
                {#if errors.password}
                    <div class="invalid-feedback">{errors.password}</div>
                {/if}
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input
                    type="password"
                    id="password_confirmation"
                    bind:value={$form.password_confirmation}
                    class="form-control"
                    placeholder="••••••••"
                    required
                />
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;" disabled={$form.processing}>
                {$form.processing ? 'Registering...' : 'Register'}
            </button>
        </form>

        <div style="margin-top: 1.5rem; text-align: center;">
            <p style="color: #7f8c8d;">
                Already have an account? 
                <a href="/login" style="color: #3498db; text-decoration: none;">Login</a>
            </p>
        </div>
    </div>
</div>

<style>
    :global(*) {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    :global(body) {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        line-height: 1.6;
        color: #333;
        background-color: #f5f5f5;
    }
    
    :global(.card) {
        background: white;
        border-radius: 8px;
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }
    
    :global(.card-header) {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e0e0e0;
    }
    
    :global(.form-group) {
        margin-bottom: 1.5rem;
    }
    
    :global(.form-label) {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }
    
    :global(.form-control) {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
    }
    
    :global(.form-control:focus) {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }
    
    .is-invalid {
        border-color: #e74c3c !important;
    }
    
    :global(.btn) {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 4px;
        font-size: 1rem;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: background-color 0.3s;
    }
    
    :global(.btn-primary) {
        background-color: #3498db;
        color: white;
    }
    
    :global(.btn-primary:hover) {
        background-color: #2980b9;
    }
    
    :global(.btn-primary:disabled) {
        background-color: #95a5a6;
        cursor: not-allowed;
    }
    
    :global(.invalid-feedback) {
        color: #e74c3c;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
</style>
