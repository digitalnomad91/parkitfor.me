#!/bin/bash

# Docker management script for parkitfor.me Laravel application
# This script provides easy commands to manage Docker containers

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored messages
print_message() {
    echo -e "${GREEN}==>${NC} $1"
}

print_error() {
    echo -e "${RED}ERROR:${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}WARNING:${NC} $1"
}

# Check if docker is available
if ! command -v docker &> /dev/null; then
    print_error "Docker is not installed. Please install Docker first."
    exit 1
fi

# Main command handling
case "$1" in
    build)
        print_message "Building Docker images..."
        docker compose build
        ;;
    up)
        print_message "Starting Docker containers..."
        docker compose up -d
        print_message "Containers started. App available at http://localhost:8080"
        ;;
    down)
        print_message "Stopping Docker containers..."
        docker compose down
        ;;
    restart)
        print_message "Restarting Docker containers..."
        docker compose restart
        ;;
    logs)
        print_message "Showing logs (press Ctrl+C to exit)..."
        docker compose logs -f "${2:-app}"
        ;;
    shell)
        print_message "Opening shell in app container..."
        docker compose exec app bash
        ;;
    artisan)
        shift
        print_message "Running artisan command: $@"
        docker compose exec app php artisan "$@"
        ;;
    composer)
        shift
        print_message "Running composer command: $@"
        docker compose exec app composer "$@"
        ;;
    npm)
        shift
        print_message "Running npm command: $@"
        docker compose exec app npm "$@"
        ;;
    migrate)
        print_message "Running database migrations..."
        docker compose exec app php artisan migrate
        ;;
    migrate-fresh)
        print_message "Running fresh migrations with seed..."
        docker compose exec app php artisan migrate:fresh --seed
        ;;
    setup)
        print_message "Setting up the application..."
        print_message "Step 1/6: Building Docker images..."
        docker compose build
        
        print_message "Step 2/6: Starting containers..."
        docker compose up -d
        
        sleep 5  # Wait for database to be ready
        
        print_message "Step 3/6: Installing Composer dependencies..."
        docker compose exec app composer install
        
        print_message "Step 4/6: Installing npm dependencies..."
        docker compose exec app npm install
        
        print_message "Step 5/6: Setting up environment file..."
        if [ ! -f .env ]; then
            docker compose exec app cp .env.docker .env
            docker compose exec app php artisan key:generate
        else
            print_warning ".env file already exists, skipping..."
        fi
        
        print_message "Step 6/6: Running migrations..."
        docker compose exec app php artisan migrate --force
        
        print_message "Setup complete! App available at http://localhost:8080"
        ;;
    status)
        print_message "Docker container status:"
        docker compose ps
        ;;
    clean)
        print_message "Cleaning up Docker resources..."
        docker compose down -v
        print_warning "This removed all containers and volumes."
        ;;
    *)
        echo "Usage: $0 {command}"
        echo ""
        echo "Available commands:"
        echo "  build              Build Docker images"
        echo "  up                 Start Docker containers"
        echo "  down               Stop Docker containers"
        echo "  restart            Restart Docker containers"
        echo "  logs [service]     Show logs (default: app)"
        echo "  shell              Open bash shell in app container"
        echo "  artisan [args]     Run artisan command"
        echo "  composer [args]    Run composer command"
        echo "  npm [args]         Run npm command"
        echo "  migrate            Run database migrations"
        echo "  migrate-fresh      Fresh migrations with seed"
        echo "  setup              Complete setup (build, install, migrate)"
        echo "  status             Show container status"
        echo "  clean              Stop containers and remove volumes"
        echo ""
        echo "Examples:"
        echo "  $0 setup                          # Complete initial setup"
        echo "  $0 artisan make:model Post        # Create a new model"
        echo "  $0 composer require package/name  # Install a package"
        echo "  $0 npm run dev                    # Run npm dev script"
        exit 1
        ;;
esac

exit 0
