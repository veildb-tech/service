# Engines

All engines should be defined as separate bundle. It is responsible for:

* creating backups (dumps)
* processing databases


## Create new Engine

To create new engine need to create service which implements `App\Service\Engine\EngineInterface` or extends abstract class `App\Service\Engine\AbstractEngine
`

After that need to add tag `app.engine` in the service.yml:

```yaml
  DbManager\MysqlBundle\Service\Engine\Mysql:
    autowire: true
    tags:
      - { name: 'app.engine' }
```