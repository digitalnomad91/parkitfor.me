<script>
    import { useForm } from '@inertiajs/svelte';
    import { inertia } from '@inertiajs/svelte';
    
    export let errors = {};
    
    const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
    
    const form = useForm({
        email: '',
        password: '',
        remember: false,
    });
    
    function handleSubmit(e) {
        e.preventDefault();
        $form.post('/login');
    }
</script>

<svelte:head>
    <title>Login - {appName}</title>
</svelte:head>

<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background-color: #f5f5f5; padding: 1rem;">
    <div class="card" style="max-width: 450px; width: 100%;">
        <div class="card-header">
            Login
        </div>

        <form on:submit={handleSubmit}>
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input
                    type="email"
                    id="email"
                    bind:value={$form.email}
                    class="form-control"
                    class:is-invalid={errors?.email}
                    placeholder="your@email.com"
                    required
                />
                {#if errors?.email}
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
                    class:is-invalid={errors?.password}
                    placeholder="••••••••"
                    required
                />
                {#if errors?.password}
                    <div class="invalid-feedback">{errors.password}</div>
                {/if}
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input
                        type="checkbox"
                        bind:checked={$form.remember}
                        style="margin-right: 0.5rem;"
                    />
                    <span>Remember Me</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;" disabled={$form.processing}>
                {$form.processing ? 'Logging in...' : 'Login'}
            </button>
        </form>

        <div style="margin-top: 1.5rem; text-align: center;">
            <p style="color: #7f8c8d;">
                Don't have an account? 
                <a href="/register" use:inertia style="color: #3498db; text-decoration: none;">Register</a>
            </p>
        </div>
    </div>
</div>

<style>
    .is-invalid {
        border-color: #e74c3c !important;
    }
</style>
