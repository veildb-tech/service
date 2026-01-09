### Installation

1. Clone this repository.
2. Clone repositotory ssh://git@gitea.bridge.digital:2222/bridgedigital/db-manager-service-frontend.git into src/frontend directory
3. Clone backend repository ssh://git@gitea.bridge.digital:2222/bridgedigital/db-manager-service.git into src/backend folder
4. Copy `cp src/backend/env-sample src/backend/.env`
5. Copy `cp env-sample .env`
6. Run `docker compose up -d`
7. Frontend should start automatically. To setup Symfony need to:
```shell
// inside of php container:
docker exec -it backup-manager-php-1 bash;
composer install;
```