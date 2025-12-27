# Inertia.js + Svelte Migration Documentation

This document describes the migration from traditional server-side rendered Blade views to a modern frontend/backend separation using Inertia.js and Svelte.

## Overview

The application has been refactored to use Inertia.js as a bridge between Laravel backend and Svelte frontend. This provides:

- Clear separation between frontend and backend code
- Modern reactive UI with Svelte components
- Client-side navigation without full page reloads
- Better developer experience with component-based architecture
- Improved maintainability and testability

## Architecture Changes

### Backend (Laravel)

#### Controllers
All controllers now return Inertia responses instead of Blade views:

```php
// Before
return view('dashboard', compact('domains', 'whoisRecords'));

// After
return Inertia::render('Dashboard', [
    'domains' => $domains,
    'whoisRecords' => $whoisRecords,
]);
```

**Modified Controllers:**
- `DashboardController` - Dashboard, domains list, WHOIS records
- `DomainController` - Domain management (create, DNS, scrapes)
- `LoginController` - Authentication
- `RegisterController` - User registration

#### Middleware
- Added `HandleInertiaRequests` middleware to share data across all pages
- Shares authentication state and flash messages globally
- Configured in `bootstrap/app.php`

#### Root Template
- Created `resources/views/app.blade.php` as the Inertia root template
- Includes Vite assets and Inertia directives

### Frontend (Svelte)

#### Directory Structure
```
resources/js/
├── Pages/
│   ├── Auth/
│   │   ├── Login.svelte
│   │   └── Register.svelte
│   ├── Domains/
│   │   ├── Create.svelte
│   │   ├── DnsRecords.svelte
│   │   ├── Scrapes.svelte
│   │   └── ScrapeDetail.svelte
│   ├── Dashboard.svelte
│   ├── Domains.svelte
│   └── WhoisRecords.svelte
├── Shared/
│   └── Layout.svelte
├── app.js
└── bootstrap.js
```

#### Components

**Layout Component (`Shared/Layout.svelte`)**
- Shared layout wrapper for authenticated pages
- Navigation bar with user info
- Flash message display
- Global styles

**Page Components**
Each page component:
- Receives data as props from the backend
- Uses Inertia links for navigation
- Uses Inertia forms for data submission
- Implements reactive UI updates

## Dependencies Added

### Backend (Composer)
- `inertiajs/inertia-laravel: ^2.0` - Laravel adapter for Inertia.js

### Frontend (NPM)
- `@inertiajs/svelte` - Svelte adapter for Inertia.js
- `svelte` - Svelte framework
- `@sveltejs/vite-plugin-svelte` - Vite plugin for Svelte

## Configuration Changes

### Vite Configuration (`vite.config.js`)
```javascript
import { svelte } from '@sveltejs/vite-plugin-svelte';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        svelte(),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@': './resources/js',
        },
    },
});
```

### App Entry Point (`resources/js/app.js`)
```javascript
import { createInertiaApp } from '@inertiajs/svelte';

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.svelte', { eager: true });
        return pages[`./Pages/${name}.svelte`];
    },
    setup({ el, App, props }) {
        new App({ target: el, props });
    },
});
```

## Usage Examples

### Creating a New Page

1. **Create Svelte Component** (`resources/js/Pages/YourPage.svelte`):
```svelte
<script>
    import Layout from '@/Shared/Layout.svelte';
    
    export let data; // Props from controller
</script>

<Layout title="Your Page">
    <h1>Your Page Content</h1>
    <p>{data.message}</p>
</Layout>
```

2. **Update Controller**:
```php
use Inertia\Inertia;

public function yourPage()
{
    return Inertia::render('YourPage', [
        'data' => ['message' => 'Hello from backend!']
    ]);
}
```

### Navigation

Use the `use:inertia` directive for links:
```svelte
<script>
    import { inertia } from '@inertiajs/svelte';
</script>

<a href="/dashboard" use:inertia>Dashboard</a>
```

### Forms

Use Inertia's `useForm` helper:
```svelte
<script>
    import { useForm } from '@inertiajs/svelte';
    
    const form = useForm({
        name: '',
        email: '',
    });
    
    function handleSubmit(e) {
        e.preventDefault();
        $form.post('/submit');
    }
</script>

<form on:submit={handleSubmit}>
    <input type="text" bind:value={$form.name} />
    <input type="email" bind:value={$form.email} />
    <button type="submit" disabled={$form.processing}>Submit</button>
</form>
```

### Accessing Shared Data

Access auth and flash data from the page store:
```svelte
<script>
    import { page } from '@inertiajs/svelte';
    
    $: user = $page.props.auth.user;
    $: flash = $page.props.flash;
</script>

{#if user}
    <p>Welcome, {user.name}!</p>
{/if}

{#if flash.success}
    <div class="alert">{flash.success}</div>
{/if}
```

## Development Workflow

### Building Assets
```bash
# Development with hot reload
npm run dev

# Production build
npm run build
```

### Running the Application
```bash
# Development (includes queue, logs, and vite)
composer dev

# Or individually
php artisan serve
npm run dev
```

## Migration Benefits

1. **Separation of Concerns**: Frontend and backend code are now clearly separated
2. **Modern Developer Experience**: Hot module replacement, component-based architecture
3. **Better Performance**: Client-side navigation, code splitting
4. **Improved Maintainability**: Component reusability, easier testing
5. **Future-Ready**: Easy to extend with additional API endpoints or mobile apps

## Backward Compatibility

The old Blade views are still present in `resources/views/` but are no longer used. They can be safely removed if desired. The following Blade views are now replaced by Svelte components:

- `dashboard.blade.php` → `Dashboard.svelte`
- `domains.blade.php` → `Domains.svelte`
- `domains/create.blade.php` → `Domains/Create.svelte`
- `domains/dns-records.blade.php` → `Domains/DnsRecords.svelte`
- `domains/scrapes.blade.php` → `Domains/Scrapes.svelte`
- `domains/scrape-detail.blade.php` → `Domains/ScrapeDetail.svelte`
- `whois-records.blade.php` → `WhoisRecords.svelte`
- `auth/login.blade.php` → `Auth/Login.svelte`
- `auth/register.blade.php` → `Auth/Register.svelte`

## Testing

The application should be tested to ensure:
1. All pages load correctly
2. Navigation works without page reloads
3. Forms submit properly and show validation errors
4. Authentication flow works (login, register, logout)
5. Flash messages display correctly
6. All domain management features work (DNS lookup, scraping, etc.)

## Troubleshooting

### Build Errors
- Ensure all NPM dependencies are installed: `npm install`
- Clear and rebuild: `npm run build`

### Hot Reload Not Working
- Check that Vite dev server is running: `npm run dev`
- Ensure port 5173 (Vite default) is not blocked

### 404 on Page Load
- Make sure assets are built: `npm run build`
- Check that Inertia middleware is registered in `bootstrap/app.php`

### Flash Messages Not Showing
- Verify flash data is shared in `HandleInertiaRequests` middleware
- Check that the Layout component is properly displaying flash messages

## Resources

- [Inertia.js Documentation](https://inertiajs.com/)
- [Svelte Documentation](https://svelte.dev/)
- [Laravel Documentation](https://laravel.com/docs)
- [Vite Documentation](https://vitejs.dev/)
