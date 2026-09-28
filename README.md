# Event Manager

## Getting started

* Copy and edit environment settings file
* Run project

```
cp .env.example .env
nano .env
docker compose up -d
```

## Development commands

The project provides a `Makefile` with shortcuts for common Docker, Drupal, and Composer commands.

```
make up              # Start containers
make upb             # Start containers and rebuild images
make build           # Build Docker images
make down            # Stop containers
make restart         # Restart containers
make bash            # Open a shell in the web container

make cr              # Clear Drupal cache
make cst             # Show Drupal configuration status
make cex             # Export Drupal configuration
make cim             # Import Drupal configuration

make composer        # Run Composer
make composer-show   # Show installed Composer packages
```
