# Docker Setup & Guide

## Overview
This project is fully dockerized to ensure a consistent development environment. It includes containers for:
- **Application**: Laravel 12 (PHP 8.2 FPM)
- **Web Server**: Nginx (Reverse Proxy)
- **Database**: MongoDB
- **Mail Server**: Mailhog (for testing emails)

## Prerequisites
- Docker & Docker Compose installed.

## Folder Structure
```
/
├── docker/
│   └── nginx/
│       └── default.conf  # Nginx configuration
├── docker-compose.yml    # Docker services definition
├── Dockerfile            # PHP App image definition
├── .dockerignore         # Files ignored by Docker build
├── .env.example          # Environment variables example
└── ...
```

## Services
| Service | Image | Internal Port | Exposed Port | Description |
|---|---|---|---|---|
| `app` | `php:8.2-fpm` | 9000 | - | Runs the Laravel backend. |
| `nginx` | `nginx:alpine` | 80 | 80 | Serves the app and proxies PHP requests. |
| `db` | `mongo:latest` | 27017 | 27017 | NoSQL Database. Data persists in `mongo_data` volume. |
| `mailhog` | `mailhog/mailhog` | 1025, 8025 | 8025 (UI) | Captures emails sent by the app. |

## Networking
All containers share a custom bridge network `chat-network`.
- `app` communicates with `db` via hostname `db` (port 27017).
- `app` sends emails to `mailhog` via hostname `mailhog` (port 1025).
- `nginx` proxies to `app` via hostname `app` (port 9000).

## Volumes
- `.:/var/www`: Code binding. Changes in your local folder are immediately reflected in the container.
- `mongo_data:/data/db`: Persistent storage for MongoDB.

## How to Run

1. **Setup Environment**:
   ```bash
   cp .env.example .env
   ```
   *Note: Ensure `DB_HOST=db` and `MAIL_HOST=mailhog` in your `.env`.*

2. **Start Containers**:
   ```bash
   docker-compose up -d --build
   ```

3. **Install Dependencies** (Inside container):
   ```bash
   docker-compose exec app composer install
   docker-compose exec app php artisan key:generate
   ```

4. **Access Application**:
   - Web: [http://localhost](http://localhost)
   - Mailhog: [http://localhost:8025](http://localhost:8025)

## Troubleshooting
- If ports are occupied, check `docker-compose.yml` to change exposed ports.
- Use `docker-compose logs -f` to watch logs.
