# Service Side

## Authentication

For authentication on service is used the module: lexik/jwt-authentication-bundle which is based on using JWT token.

**Before using need to create key-pairs**:

`php bin/console lexik:jwt:generate-keypair`

There are 2 ways for authentication:

* with Login ( email ) and password - this default one, used user entity as source of data
* with Server UUID and Server Secret Key - this is additional and using only for access to API

Both of types return as result JWT token which must be used for any API request. Those token must be added to header:

```javascript
Authorization: Bearer < token >
```

To use Server credentials you must:

* add an additional header: `Authorization-Type: Token`
* a server must be in Enabled status

Steps to get Server JWT token:

* need to send on URL: [/api/token\_check](https://db-manager-service.local/api/token_check) POST request with Json data:
  * uuid:
  * secret\_key:

Example:

```javascript
curl --location 'https://db-manager-service.local/api/token_check' \
--header 'Content-Type: application/json' \
--data '{
    "uuid": "01892b7f-c42b-7e8a-9691-bfce511abbca",
    "secret_key": "01892b7f"
}'
```

## Public API:

Server:

1.  POST: /api/servers -

    OST array:

    1.  \\

        ```javascript
        {
          "name": "string",
          "uuid": "<UUID>",
          "status": "<enabled | disabled | pending>",
          "databases": [],
          "workspaceId": <workspace ID>
        }
        ```

## DB Cleaning rules

Main structure for whole table:

```javascript
<table name> => [
   method => truncate | update | fake
   where => <rules>
],
```

Structure for updating columns of table:

```javascript
<table name> => [
  'columns' => [
    <column name> => [
      'method' => truncate | update | fake,
      'value'  => final value,
      'where'  => <rules>,
    ],
  ],
],
```

Example:

```php
'sales_order' => [
  'method' => 'truncate',
  'where' => 'entity_id != 67',
],
'adminnotification_inbox' => [
  'method' => 'truncate',
],
'admin_user' => [
  'method' => 'truncate|fake',
  'fake_data' => [
    'email' => 'admin@gmail.com',
    'username' => 'admin',
    'password' => 'admin'
  ]
],
'customer_entity' => [
  'columns' => [
    'email' => [
      'method' => 'update',
      'value'  => "CONCAT ('test_', email)",
      'where'  => "email NOT LIKE ('%@overdose.digital')",
    ],
    'firstname' => [
      'method' => 'update',
      'value'  => 'null',
      'where'  => "created_in LIKE '%NZ Store%' OR lastname = 'Miles'",
    ],
  ],
],
'customer_entity_varchar' => [
  'columns' => [
    'value' => [
      'method' => 'update',
      'value'  => "md5('Admin123')",
      'where'  => "attribute_id IN (SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'password_hash' AND entity_type_id = 1)"
    ],
  ],
]
```

## Backup Cleaning Rules:

List or backups for deleting will be returned by URL: api/servers//get\_dump\_delete\_list.

The URL will take all DB related to server, get Server rules and according to rules will return array:

```javascript
[
  [
    'db_uuid' => 'UUID of DB',
    'filename' => 'backup file name'
  ],
  ...
]
```

Rules on service side must be saved into the next variant:

```json
{
  "<rule_id>": {
    "rule":  "gt"
    "value": "PT20H"
  }
}
```

In field "rule" could be used:

* gt => '>'
* lt => '<'

In field value could be used

* date in format: **ISO 8601 (** [**https://www.digi.com/resources/documentation/digidocs//90001488-13/reference/r\_iso\_8601\_duration\_format.htm**](https://www.digi.com/resources/documentation/digidocs/90001488-13/reference/r_iso_8601_duration_format.htm) **)**
