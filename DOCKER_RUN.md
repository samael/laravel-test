# Docker Local Run Guide

This guide explains how to run the project locally with Docker.

## Prerequisites

- Docker Desktop (or Docker Engine + Docker Compose)
- Free ports:
  - `8000` for the Laravel app
  - `33060` for MySQL

## Included Services

The Docker setup includes:

- `app` - PHP 8.4 container running Laravel on port `8000`
- `db` - MySQL 8.4 container on port `33060`

## First Start

From the project root, run:

```bash
docker compose up -d --build
```

Then run database migrations and seeders:

```bash
docker compose exec app php artisan migrate --seed
```

Open the app:

- http://localhost:8000

## Daily Commands

Start containers:

```bash
docker compose up -d
```

Stop containers:

```bash
docker compose down
```

View logs:

```bash
docker compose logs -f app
docker compose logs -f db
```

Run Artisan commands:

```bash
docker compose exec app php artisan about
docker compose exec app php artisan route:list
```

Run tests:

```bash
docker compose exec app php artisan test
```

## Full Reset

If you need a clean database state:

```bash
docker compose down -v
docker compose up -d --build
docker compose exec app php artisan migrate --seed
```

## Troubleshooting

### Port already in use

If `8000` or `33060` is occupied, change the published ports in `docker-compose.yml`.

### Permission or missing dependency issues

Rebuild images:

```bash
docker compose build --no-cache
docker compose up -d
```

### App key or env file issues

The container entrypoint creates `.env` from `.env.docker.example` on first run.
You can regenerate app key manually:

```bash
docker compose exec app php artisan key:generate
```
