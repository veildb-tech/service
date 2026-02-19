# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

DBVisor is a full-stack database monitoring/management tool. The monorepo contains:
- **Backend**: Symfony 6.2 (PHP 8.2+) with GraphQL via API Platform
- **Frontend**: Next.js 16 (React 19) with TypeScript and Apollo Client
- **Infrastructure**: Docker Compose (PHP-FPM, Nginx, Node.js, MySQL 8.0)

## Common Commands

All services run in Docker. Use these commands from the repo root:

```bash
# Start development environment (includes phpmyadmin, hot reload)
./dev.sh

# Or manually:
docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d

# View logs
docker compose logs -f [php|nginx|frontend|database]

# Stop all services
docker compose down
```

### Backend (Symfony)

```bash
# Install/update PHP dependencies
docker compose exec php composer install

# Run database migrations
docker compose exec php php bin/console doctrine:migrations:migrate

# Generate a single new migration
docker compose exec php php bin/console doctrine:migrations:diff

# Regenerate JWT keys
docker compose exec php php bin/console lexik:jwt:generate-keypair

# Run tests
docker compose exec php php bin/phpunit

# Run a single test file
docker compose exec php php bin/phpunit path/to/TestFile.php
```

### Frontend

```bash
# All frontend commands run inside the container
docker compose exec frontend yarn lint
docker compose exec frontend yarn lint-fix
docker compose exec frontend yarn build

# Or run locally if Node.js is installed:
cd src/frontend
yarn install
yarn dev      # dev server on port 80
yarn build
yarn lint
yarn lint-fix
```

## Architecture

### Request Flow

```
Browser → Nginx (:80) → PHP-FPM → Symfony → GraphQL Resolver → Service → Doctrine ORM → MySQL
Browser → Next.js (:3000) → Apollo Client → same Nginx/GraphQL endpoint
```

### Backend Structure (`src/backend/src/`)

- **Controller/**: HTTP entry points — `Api/GraphQLController` is the main GraphQL endpoint, `Security/` handles JWT login/refresh
- **Resolver/**: GraphQL resolvers implementing `QueryItemResolverInterface` or `MutationResolverInterface` — one per query/mutation
- **Service/**: Business logic, organized by domain (Database, Workspace, Server, Webhook)
- **Entity/**: Doctrine ORM entities grouped by domain (Workspace, Database, Admin)
- **Repository/**: Doctrine repositories for each entity
- **Voter/**: Symfony voters handle entity-level authorization

### Frontend Structure (`src/frontend/src/`)

- **pages/**: Next.js routes — mirrors domain structure (auth, databases, rules, servers, manage, account)
- **sections/**: Feature modules containing the main UI logic per page
- **components/**: Reusable UI components (database, rule, grid, etc.)
- **queries/**: Apollo Client GraphQL queries/mutations
- **contexts/**: React Context providers (e.g., RuleContext)
- **guards/**: Auth protection wrappers for pages
- **layouts/**: DashboardLayout and AuthLayout
- **theme/**: MUI theme configuration with custom color palette (`dbm-color-*`)

### Key Domain Concepts

- **Workspace**: Top-level organization container; all entities belong to a workspace
- **Database**: A monitored MySQL database with connection config
- **DatabaseRule**: Compliance/quality rules applied to databases (based on rule templates in `/src/backend/rule_templates/`)
- **Server**: Database server host configuration
- **Group**: User group for workspace permissions
- **Webhook**: Event notification integrations

### Authentication

JWT-based auth:
1. POST `/api/login_check` → returns access + refresh tokens
2. Frontend stores tokens in cookies (`react-cookie`)
3. All `/api/*` and `/api/graphql` requests require `Authorization: Bearer <token>` header
4. Refresh via POST `/api/token/refresh`
5. JWT keypair is auto-generated on container startup from `JWT_PASSPHRASE` env var

### Environment Setup

Two `.env` files are required:
- `src/backend/.env` — database URL, JWT config, mailer, CORS (copy from `src/backend/.env-sample`)
- `src/frontend/.env.local` — GraphQL endpoint URLs (copy from `src/frontend/env-sample`)

Root `.env` controls Docker port mapping (copy from `env-sample`):
```
FRONTEND_PORT=3000
NGINX_PORT=80
PHPMYADMIN_PORT=8080
```

The `install.sh` script automates the full first-time setup including JWT key generation and running migrations.

### GraphQL Schema

The GraphQL API is served at `/api/graphql`. Resolvers are registered in `config/services.yaml` or via Symfony autowiring with tags. Each resolver handles one query or mutation type and delegates to the Service layer.

### Database Migrations

71+ migration files in `src/backend/migrations/`. Migrations run automatically on container startup via the Docker entrypoint script. Always generate migrations via `doctrine:migrations:diff` rather than writing them manually.

## Frontend Package Notes

**Node.js version**: The project uses Node.js 23, but ESLint 10 only officially supports `^20.19.0 || ^22.13.0 || >=24`. The `.yarnrc` in `src/frontend/` sets `--ignore-engines true` to allow installation anyway (ESLint 10 runs fine on Node 23 despite the engine field).

**Apollo Client v4 import paths**: In v4, React hooks were moved out of the main package. Always import hooks from the correct subpaths:
- `gql`, `ApolloClient`, `InMemoryCache`, `createHttpLink`, `from` → `@apollo/client`
- `useQuery`, `useMutation`, `useLazyQuery`, `ApolloProvider` → `@apollo/client/react`
- `setContext` → `@apollo/client/link/context`
- `onError` → `@apollo/client/link/error`

**Tailwind CSS v4**: PostCSS plugin moved to `@tailwindcss/postcss`. CSS `@import` for local files must use `./` prefix. Use `@config "../../tailwind.config.js"` to load the JS config, and `@import "tailwindcss"` instead of the old `@tailwind base/components/utilities` directives.

**MUI v7**: `Unstable_Grid2` was renamed to `Grid` (the legacy v1 Grid is now `GridLegacy`).

**Next.js 16**: The `middleware.js` convention is deprecated; use `proxy.js` with a `proxy` (or `default`) function export. The `eslint` key in `next.config.js` was removed.
