# Clearing Rules

## Rules suggestions:

Triggers on:

* saving - db entity
* updating - db entity


Code source: src/Service/Database/GenerateSuggestedRule.php

The functionality:

* gets DB structure and additional data
* according to DB platforms the process analyze and generates array with suggestions

Possible templates is located in file:

* config/rules/generator_patterns.yaml

Patterns has 4 levels of checking:


1. - term - just check is term in column name: str_contains
2. - column_pattern - check with more flexible pattern: preg_match_all
3. - column_pattern_precision - it precision of result
4. - table_pattern - pattern for checking is table could be processed, ex: employee > name

Rules which must be applied are describing in array: rule

Example:

```javascript
mail:
    term: mail
    rule:
        method: fake
        value: safeEmail
first_name:
    column_pattern: '/(first|name)/i'
    column_pattern_precision: 2
    rule:
        method: fake
        value: firstName
name:
    term: name
    table_pattern: '/(user|employee)/i'
    table_pattern_precision: 1
    rule:
        method: fake
        value: name
```


Example of generated rule array:

```javascript
[
    {
        "table":"customer_grid_flat",
        "columns":[
            {
                "method":"fake",
                "name":"email",
                "value":"safeEmail"
            },
            {
                "method":"fake",
                "name":"billing_firstname",
                "value":"firstName"
            },
            {
                "method":"fake",
                "name":"billing_lastname",
                "value":"lastName"
            },
            {
                "method":"fake",
                "name":"billing_telephone",
                "value":"phoneNumber"
            }
        ]
    },
    {
        "table":"adobe_user_profile",
        "columns":[
            {
                "method":"fake",
                "name":"email",
                "value":"safeEmail"
            }
        ]
    }
]
```


\

\