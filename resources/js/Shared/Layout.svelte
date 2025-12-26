<script>
    import { inertia } from '@inertiajs/svelte';
    import { page, router } from '@inertiajs/svelte';

    export let title = '';
    
    $: auth = $page.props.auth;
    $: flash = $page.props.flash;
    
    function logout() {
        router.post('/logout');
    }
</script>

<svelte:head>
    <title>{title ? `${title} - ` : ''}{import.meta.env.VITE_APP_NAME || 'Laravel'}</title>
</svelte:head>

<div class="app-container">
    {#if auth?.user}
    <nav class="navbar">
        <div class="navbar-content">
            <a href="/dashboard" use:inertia class="navbar-brand">
                {import.meta.env.VITE_APP_NAME || 'Laravel'}
            </a>
            <ul class="navbar-nav">
                <li><a href="/dashboard" use:inertia>Dashboard</a></li>
                <li><a href="/domains" use:inertia>Domains</a></li>
                <li><a href="/whois-records" use:inertia>WHOIS Records</a></li>
                <li>
                    <button on:click={logout} type="button">Logout</button>
                </li>
            </ul>
        </div>
    </nav>
    {/if}

    <div class="container">
        {#if flash?.success}
            <div class="alert alert-success">
                {flash.success}
            </div>
        {/if}

        {#if flash?.error}
            <div class="alert alert-error">
                {flash.error}
            </div>
        {/if}

        <slot />
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
    
    .navbar {
        background-color: #2c3e50;
        color: white;
        padding: 1rem 2rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .navbar-content {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .navbar-brand {
        font-size: 1.5rem;
        font-weight: bold;
        color: white;
        text-decoration: none;
    }
    
    .navbar-nav {
        display: flex;
        gap: 2rem;
        list-style: none;
        align-items: center;
    }
    
    .navbar-nav :global(a) {
        color: white;
        text-decoration: none;
        transition: opacity 0.3s;
    }
    
    .navbar-nav :global(a:hover) {
        opacity: 0.8;
    }
    
    .navbar-nav button {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        font-size: 1rem;
        transition: opacity 0.3s;
    }
    
    .navbar-nav button:hover {
        opacity: 0.8;
    }
    
    .container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
    }
    
    .alert {
        padding: 1rem;
        border-radius: 4px;
        margin-bottom: 1rem;
    }
    
    .alert-success {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }
    
    .alert-error {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
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
    
    :global(.btn-secondary) {
        background-color: #95a5a6;
        color: white;
    }
    
    :global(.btn-secondary:hover) {
        background-color: #7f8c8d;
    }
    
    :global(.invalid-feedback) {
        color: #e74c3c;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    :global(table) {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }
    
    :global(th), :global(td) {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #e0e0e0;
    }
    
    :global(th) {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    
    :global(tr:hover) {
        background-color: #f8f9fa;
    }
    
    :global(.text-center) {
        text-align: center;
    }
    
    :global(.mt-3) {
        margin-top: 1rem;
    }
    
    :global(.grid) {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    :global(.stat-card) {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-left: 4px solid #3498db;
    }
    
    :global(.stat-card h3) {
        font-size: 0.875rem;
        color: #7f8c8d;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    :global(.stat-card .stat-value) {
        font-size: 2rem;
        font-weight: bold;
        color: #2c3e50;
    }
</style>
