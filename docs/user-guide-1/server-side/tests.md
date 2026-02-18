# Tests

There is separate bundle for tests called TestBundle. Main purpose of this bundle is to provide debug and test console commands and unit tests to verify all works as expected.


There is jenkins script where u can check how it works:

```bash
pipeline {
    agent any
    environment { 
        STEAM = 'db-manager'
        TOPIC = 'Jenkins'
        MESSAGE = "Test job has finished with status ${currentBuild.currentResult}"
    }
    stages {
        stage('Deploy code') {
            steps {
                git branch: 'main', credentialsId: 'e61663fd-5692-409f-aa49-8705d1ba393a', url: 'https://gitea.bridge.digital/bridgedigital/db-manager-server-skeleton.git'
                dir('src/server') {
                    git branch: 'main', credentialsId: 'e61663fd-5692-409f-aa49-8705d1ba393a', url: 'https://gitea.bridge.digital/bridgedigital/db-manager-server-symfony.git'
                    sh 'cp env-example .env'
                    sh "sed -i 's/APP_SERVER_UUID=/APP_SERVER_UUID=${APP_SERVER_UUID}/g' .env"
                    sh "cat .env"
                }
                sh 'cp env-example .env'
                sh 'docker compose up -d'
                sh 'docker exec -w /app/symfony -i dbm_app composer install'
            }
        }
        
        stage('Preparing database') {
            steps {
                sh 'mkdir dumps/untouched/${DB_UUID}'
                sh 'cp ${DUMP_PATH} dumps/untouched/${DB_UUID}/test_db1.sql'
                sh "docker exec -i dbm_app mysql -u${DATABASE_USER} -p${DATABASE_PASSWD} -h${DATABASE_HOST} -P${DATABASE_PORT} -e'CREATE DATABASE ${DATABASE_NAME}'"
                sh "docker exec -i dbm_app mysql -u${DATABASE_USER} -p${DATABASE_PASSWD} -h${DATABASE_HOST} -P${DATABASE_PORT} ${DATABASE_NAME} < dumps/untouched/${DB_UUID}/test_db1.sql"
                sh "docker exec -i dbm_app php /app/symfony/bin/console app:server:update --current --email=ihor.k@bridge.digital --password=qwe123"

                sh "docker exec -e DATABASE_USER=${DATABASE_USER} -e DATABASE_PASSWD=${DATABASE_PASSWD} -e DATABASE_HOST=${DATABASE_HOST} -e DATABASE_PORT=${DATABASE_PORT} -i dbm_app php /app/symfony/bin/console app:db:process-debug --uuid=${DB_UUID} --db_name=${DATABASE_NAME}"
            }
        }
        stage('Run tests') {
            steps {
                dir('src/server') {
                    sh 'cp phpunit.xml.dist phpunit.xml'
                }
                sh "docker exec -e DATABASE_USER=${DATABASE_USER} -e DATABASE_PASSWD=${DATABASE_PASSWD} -e DATABASE_HOST=${DATABASE_HOST} -e DATABASE_PORT=${DATABASE_PORT} -e DATABASE_NAME=${DATABASE_NAME} -w /app/symfony -i dbm_app php /app/symfony/vendor/bin/phpunit"
            }
        }
    }
    post { 
        always { 
            sh 'docker compose down'
            cleanWs()
        
            withCredentials([usernamePassword(credentialsId: 'zulip-api', passwordVariable: 'PASSWORD', usernameVariable: 'EMAIL')]) {
                sh('curl -X POST https://zulip.bridge.digital/api/v1/messages -u $EMAIL:$PASSWORD --data-urlencode type=stream --data-urlencode "to=$STEAM" --data-urlencode topic=$TOPIC --data-urlencode "content=$MESSAGE"')
            }
        }
    }
}
```


How it works:


1. First of all, there is pre-configured database test test_db1.sql with "original" data. Additionally, there is data/origin.json file with this data in the test bundle. Also, everything is configured on the service side as it is real database but it is assigned to BD workspace
2. After database is setup it run `app:db:process-debug --uuid=${DB_UUID} --db_name=${DATABASE_NAME}"` which basically is same as `app:db:process` but it don't check if database is scheduled and doesn't create dump so it just get rules by `uuid` option and process directly in `--db_name` database.
3. After database processed according to rules from the service it run unit tests which compare data in database and origin.json file. Data should be updated according to rules.