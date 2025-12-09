# parkitfor.me

Laravel application for domain management with WHOIS lookup functionality.

## Features

- **User Authentication**: Complete registration and login system using Laravel sessions
- **Domain Management**: Store and manage domain information including registrar, expiration dates, and status
- **WHOIS Lookup**: CLI command to perform WHOIS lookups on domains
- **Dashboard**: View domain and WHOIS record statistics with paginated tables
- **Relationship Tracking**: Each domain can have multiple WHOIS lookup records

## Installation

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   ```

3. Copy `.env.example` to `.env` and configure your database:
   ```bash
   cp .env.example .env
   ```

4. Generate application key:
   ```bash
   php artisan key:generate
   ```

5. Run migrations:
   ```bash
   php artisan migrate
   ```

6. (Optional) Seed sample data:
   ```bash
   php artisan db:seed --class=DomainSeeder
   ```

## Usage

### Starting the Application

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

### Authentication

1. Register a new account at `/register`
2. Login at `/login`
3. Access the dashboard after authentication

### WHOIS Lookup CLI

Lookup a specific domain:
```bash
php artisan whois:lookup example.com
```

Lookup all domains in the database:
```bash
php artisan whois:lookup --all
```

### Dashboard Features

- **Dashboard**: Overview with statistics and recent data
- **Domains**: Complete list of all domains with pagination
- **WHOIS Records**: History of all WHOIS lookups with detailed information

## Database Schema

### Domains Table
- `id`: Primary key
- `name`: Domain name (unique)
- `tld`: Top-level domain
- `registered_at`: Registration date
- `expires_at`: Expiration date
- `registrar`: Domain registrar
- `status`: Domain status (active, inactive, etc.)
- `nameservers`: Nameserver information
- `notes`: Additional notes
- `timestamps`: Created/updated timestamps

### WHOIS Records Table
- `id`: Primary key
- `domain_id`: Foreign key to domains table
- `raw_whois_data`: Raw WHOIS response
- `registrar`: Registrar name
- `creation_date`: Domain creation date
- `expiration_date`: Domain expiration date
- `updated_date`: Last update date
- `nameservers`: JSON array of nameservers
- `status`: Domain status
- `registrant_name`: Registrant name
- `registrant_email`: Registrant email
- `registrant_organization`: Registrant organization
- `timestamps`: Created/updated timestamps

## Requirements

- PHP 8.1 or higher
- Composer
- SQLite/MySQL/PostgreSQL

## License

Open source.
