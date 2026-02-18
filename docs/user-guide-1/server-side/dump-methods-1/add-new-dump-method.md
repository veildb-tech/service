# Add New Dump Method

To add new dump method need to create new service which implements `App\Service\Methods\MethodInterface` and implement all required methods. After service is ready it should be passed to `App\Service\Methods\MethodProcessor` service via tag `app.backup_method` like:

```yaml
 DbManager\MysqlBundle\Service\Methods\MysqldumpOverSsh:
    autowire: true
    tags:
      - { name: 'app.backup_method' }
```


### 1. Specify name and description of the method  

First of all need to specify name and description of method like:

```php

    public function getCode(): string
    {
        return 'aws-s3';
    }

    public function getDescription(): string
    {
        return 'AWS S3';
    }
```


### 2. Second step is to configure prompts to ask all required configs:


```php
    public function askConfig(InputOutput $inputOutput): array
    {
        $validateRequired = function ($value) {
            if (empty($value)) {
                throw new \RuntimeException('Value is required.');
            }

            return $value;
        };

        return [
            'aws_s3_key' => $inputOutput->ask("AWS Key", null, $validateRequired),
            'aws_s3_secret' => $inputOutput->ask("AWS Secret", null, $validateRequired),
            'aws_s3_bucket' => $inputOutput->ask("Bucket name", null, $validateRequired),
            'aws_s3_region' => $inputOutput->ask("Region", null, $validateRequired),
            'aws_s3_version' => $inputOutput->ask("Version", 'latest', $validateRequired),
            'aws_s3_filename' => $inputOutput->ask("Filename", 'backup.sql', $validateRequired),
        ];
    }
```


### 3. Configure validator

Third step is to configure validator (you can skip it and return always true if you don't need additional validation). Example with AWS S3 below:

```php
    public function validate(array $config): bool
    {
        $client = $this->getClient($config);
        $objects = $client->listObjects([
            'Bucket' => $config['aws_s3_bucket']
        ]);

        $found = false;
        foreach ($objects['Contents']  as $object) {
            if ($object['Key'] === $config['aws_s3_filename']) {
                $found = true;
            }
        }

        return $found;
    }
```


So basically in validation need to ensure file exists and connection is OK.

### 4. Configure dump creation

Last step is to configure backuping of database. Example:

```php
   public function execute(array $dbConfig, string $dbUuid, ?string $filename = null): string
    {
        $destFile = $this->getDestinationFile($dbUuid, $filename);
        $client = $this->getClient($dbConfig);
        $client->getObject(array(
            'Bucket' => $dbConfig['aws_s3_bucket'],
            'Key' => $dbConfig['aws_s3_filename'],
            'SaveAs' => $destFile
        ));

        return $destFile;
    }
```

Here method `execute` accepts configurations, DB UUID and filename. This method should return full path to backup file;