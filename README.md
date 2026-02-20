### Installation

#### Quick Installation (Recommended)

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

#### Manual Installation

If you prefer to install manually:

1. Clone this repository.
2. Clone repository `ssh://git@gitea.bridge.digital:2222/bridgedigital/db-manager-service-frontend.git` into `src/frontend` directory
3. Clone backend repository `ssh://git@gitea.bridge.digital:2222/bridgedigital/db-manager-service.git` into `src/backend` folder
4. Copy environment files:
   ```bash
   cp env-sample .env
   cp src/backend/env-sample src/backend/.env
   cp src/frontend/env-sample src/frontend/.env
   ```
5. Generate JWT passphrase and update `src/backend/.env`:
   ```bash
   # Generate a random passphrase
   openssl rand -base64 32 | tr -d "=+/" | cut -c1-32
   # Add it to JWT_PASSPHRASE in src/backend/.env
   ```
6. Start Docker Compose:
   ```bash
   docker compose up -d --build
   ```
7. Install backend dependencies:
   ```bash
   docker compose exec php composer install
   ```
8. Generate JWT keys:
   ```bash
   docker compose exec php php bin/console lexik:jwt:generate-keypair
   ```
9. Run database migrations:
   ```bash
   docker compose exec php php bin/console doctrine:migrations:migrate
   ```
10. Frontend dependencies will be installed automatically when the container starts.

#### Services

After installation, services will be available at: http://localhost:8080 (or port specified in `.env` as `NGINX_PORT`)

#### Troubleshooting

- If services fail to start, check logs: `docker compose logs`
- To rebuild containers: `docker compose up -d --build`
- To stop services: `docker compose down`