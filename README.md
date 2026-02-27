# VeilDB Service

Web-based control center for managing database anonymization rules and infrastructure.

The Service is the brain of VeilDB. It stores configuration, manages access, and coordinates agents.

---

## Part of VeilDB

This repository is **part** of the VeilDB platform.

- Main project overview: [https://github.com/veildb-tech](https://github.com/veildb-tech)
- Documentation: [https://veildb.gitbook.io/](https://veildb.gitbook.io/)

---

## Responsibilities

- Configure data masking rules
- Control anonymization frequency
- Manage user access and permissions
- View processing logs and history
- Manage webhooks

---

## How It Fits

The Service does not process databases directly.

It sends rules and instructions to Agents, which perform the actual backup and anonymization process.

---

## Typical Flow

1. Add database source
2. Configure masking rules
3. Assign permissions
4. Agent processes dump
5. Developers download anonymized database

---

## Demo

![Service Demo](https://github.com/veildb-tech/.github/blob/main/profile/demo.gif)

The demo shows rule configuration, triggering a dump, and verifying masked output.

---

## Installation

### Quick Installation (Recommended)

Run the automated installation script:

```bash
./install.sh
```

This script will:
1. Set up all environment files (`.env`) from `env-sample` files
2. Generate JWT passphrase automatically
3. Start Docker Compose services
4. Install backend dependencies via Composer
5. Set up the database and run migrations
6. Generate JWT keys
7. Install frontend dependencies

### Manual Installation

If you prefer to install manually:

1. Clone this repository.
2. Copy environment files:
   ```bash
   cp env-sample .env
   cp src/backend/env-sample src/backend/.env
   cp src/frontend/env-sample src/frontend/.env
   ```
3. Generate JWT passphrase and update `src/backend/.env`:
   ```bash
   # Generate a random passphrase
   openssl rand -base64 32 | tr -d "=+/" | cut -c1-32
   # Add it to JWT_PASSPHRASE in src/backend/.env
   ```
4. Start Docker Compose:
   ```bash
   docker compose up -d --build
   ```
5. Install backend dependencies:
   ```bash
   docker compose exec php composer install
   ```
6. Generate JWT keys:
   ```bash
   docker compose exec php php bin/console lexik:jwt:generate-keypair
   ```
7. Run database migrations:
   ```bash
   docker compose exec php php bin/console doctrine:migrations:migrate
   ```
8. Frontend dependencies will be installed automatically when the container starts.

### Local Access

After installation, services will be available at: http://localhost:8080 (or port specified in `.env` as `NGINX_PORT`)


- If services fail to start, check logs: `docker compose logs`
- To rebuild containers: `docker compose up -d --build`
- To stop services: `docker compose down`
