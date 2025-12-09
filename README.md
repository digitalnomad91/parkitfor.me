# parkitfor.me

Laravel application for domain management with WHOIS lookup functionality.

## Features

- **User Authentication**: Complete registration and login system using Laravel sessions
- **Domain Management**: Store and manage domain information including registrar, expiration dates, and status
- **WHOIS Lookup**: CLI command to perform WHOIS lookups on domains
- **Dashboard**: View domain and WHOIS record statistics with paginated tables
- **Relationship Tracking**: Each domain can have multiple WHOIS lookup records

## Installation

### Option 1: Local Installation

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

### Option 2: Docker Installation (Recommended for Production)

The application is configured to run in Docker with a MySQL database on a shared network.

1. Clone the repository

2. **Quick Setup (using the helper script):**
   ```bash
   ./docker.sh setup
   ```

   Or manually:

3. Copy the Docker environment file:
   ```bash
   cp .env.docker .env
   ```

4. Build and start the Docker containers:
   ```bash
   npm run docker:setup
   # or
   composer docker:setup
   ```

This will:
- Build the Docker images
- Start the containers (app on port 8080, database on port 33060)
- Install PHP and Node.js dependencies
- Generate the application key
- Run database migrations

**Docker Helper Script:**

The `docker.sh` script provides convenient commands:

```bash
./docker.sh setup              # Complete initial setup
./docker.sh up                 # Start containers
./docker.sh down               # Stop containers
./docker.sh restart            # Restart containers
./docker.sh logs               # View logs
./docker.sh shell              # Access container shell
./docker.sh artisan [command]  # Run artisan commands
./docker.sh composer [command] # Run composer commands
./docker.sh npm [command]      # Run npm commands
./docker.sh migrate            # Run migrations
./docker.sh status             # Show container status
./docker.sh clean              # Remove containers and volumes
```

**Available Docker Commands:**

Using npm:
```bash
npm run docker:build          # Build Docker images
npm run docker:up             # Start containers
npm run docker:down           # Stop containers
npm run docker:restart        # Restart containers
npm run docker:logs           # View container logs
npm run docker:shell          # Access container shell
npm run docker:migrate        # Run migrations
npm run docker:migrate:fresh  # Fresh migrations with seed
```

Using composer:
```bash
composer docker:build          # Build Docker images
composer docker:up             # Start containers
composer docker:down           # Stop containers
composer docker:restart        # Restart containers
composer docker:logs           # View app logs
composer docker:shell          # Access container shell
composer docker:artisan        # Run artisan commands
composer docker:composer       # Run composer commands
composer docker:npm            # Run npm commands
composer docker:migrate        # Run migrations
composer docker:migrate:fresh  # Fresh migrations with seed
```

**Accessing the Application:**
- Application: http://localhost:8080
- Database (from host): localhost:33060

**Network Configuration:**
The Docker setup uses:
- Internal network: `parkitfor_network` (bridge driver)
- App exposed on host port: 8080 (maps to container port 80)
- Database exposed on host port: 33060 (maps to container port 3306)
- Containers communicate internally on the shared network

## Usage

### Starting the Application

**Local:**
```bash
php artisan serve
```
Visit `http://localhost:8000` in your browser.

**Docker:**
```bash
npm run docker:up
# or
composer docker:up
```
Visit `http://localhost:8080` in your browser.

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
