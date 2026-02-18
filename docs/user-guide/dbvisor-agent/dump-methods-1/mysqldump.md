# Mysqldump

If you are using a Docker setup, there are additional configurations that may be required, particularly due to MySQL restrictions on connecting from a Docker container. Follow these steps:

1. By default, the tool uses the network with the next Subnet: 172.27.0.0/16.

{% hint style="info" %}
It can be changed by using the variable: DBVISOR\_SUBNET
{% endhint %}

Open MySQL configurations (usually path to configs: `/etc/mysql/mysql.conf.d/mysqld.cnf` ) , check is config already has bind-address, in case yes then add new via semicolon ( without space ):

```javascript
bind-address = 127.0.01,172.27.0.1
```

in other case, adda  new line:

```bash
bind-address = 172.27.0.1
```

Bind address must be equivalent to db visor subnet address.

2.  The second step is to add privileges for user to connect from subnet IP. Connect to mysql

    To add new user (optional)

    ```sql
    CREATE USER '< User >'@'172.27.0.0/16' IDENTIFIED BY '< Password >';
    ```

    Allow to connect from subnet:

    ```sql
    GRANT ALL PRIVILEGES ON *.* TO '< User >'@'172.27.0.0/16' WITH GRANT OPTION;
    FLUSH PRIVILEGES;
    ```

**Note:**

* You need to replace `<User>` with your actual MySQL username and `<Password>`with the corresponding password.
* If you changed the default subnet value (`DBVISOR_SUBNET`, make sure to adjust the IP addresses accordingly.
