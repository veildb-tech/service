# Server Side

## Server Side

## Symfony

Current structure of the application is following:

### Available commands:

```javascript
php bin/consosle app:db:process --uid=<Database UID> --db=<Database Name>
php bin/console app:db:getScheduled
php bin/console app:db:log --uuid=<Backup UUID> --status=<Process Status> --message=<Message>
php bin/console app:db:finish --uuid=<Backup UUID> --status=<Process Status> --filename=<backup file name>

php bin/console app:server:generate-keypair
php bin/console app:db:backups:clear
```

#### Create new processor

To create new processor need to create new bundle which extends from DbManager\_CoreBundle. Need to create service.yaml file:

```yaml
services:
  db_manager_core.engines.<engine_name>:
    public: true
    class: DbManager\<Engine>Bundle\Service\EngineProcessor
    arguments: []
```

Where \<engine\_name> should be changed to engine name. It should be similar to which is specified in the service.

Class **DbManager\Bundle\Service\EngineProcessor** should implement **DbManager\CoreBundle\Interfaces\EngineInterface** interface.

Note: **Bundle** it could be any name of bundle.

## Database Configurations

Configurations to databases should be stored under \<app\_directory>/config/\<db\_uuid>/config with following formats:

```yaml
METHOD=dump
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=root123
DB_NAME=test5
DB_PORT=3306
NAME=Test3
ENGINE=mysql
```

Example for manual dump

```yaml
METHOD=manual
DUMP_NAME=test.sql
```

### Server Configurations

To get access to the service API with server credentials need to fill next params in .env file:

```javascript
APP_SERVER_UUID=
APP_SERVER_SECRET_KEY=
```

Those params will be used to get JWT token for authentication.

Params could be filled in 2 ways:

* manually
* automatically during adding / updating server data, in this case existed data will be overridden by new data

### Generating SSL Keys

To get the ability to download DB from a server by client request will be required 2 SSL Keys:

* public on client side
* private on server side

To generate keys need to execute the command:

```javascript
php bin/console app:server:generate-keypair
```

The command will require a key par owner and as a result, will return the generated public key which must be added on the client side.

#### Required configurations:

On sever side in .env file:

* SECRET\_KEY\_PRIVATE=
* SECRET\_KEY\_PUBLIC=

On the client side, in .env file:

```javascript
KEY_FILE=<path to public key>
```

#### Removing backups:

Are backups could be deleted in two ways:

1. manually
2. automatically via service rules

To delete automatically need to execute the command:

```javascript
php bin/console app:db:backups:clear
```

It takes APP\_SERVER\_UUID and send request to service API Url: api/servers//get\_dump\_delete\_list. The API request will return are list of db files which must be deleted.
