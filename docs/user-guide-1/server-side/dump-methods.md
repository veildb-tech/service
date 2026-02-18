# Dump Methods

### Currently supported dump methods


1. **AWS S3**
2. **Dump** - this method create dump from database which is stored directly on the same server or has public IP (url) to connect.

   In case the used tool is based on docker a customer must do an additional operations to set connections:

   ```json
   By default, the tool uses the network with the next Subnet: 172.27.0.0/16.
   It can be changed by using the variable: DBVISOR_SUBNET
   
   MySQL
   1. open the file: /etc/mysql/mysql.conf.d/mysqld.cnf
      - add to parameter: bind-address - 172.27.0.1 via semicolon ( in case you left default Subnet value )
   2. add access to your user with mysql commands:
      - CREATE USER '< User >'@'172.27.0.0/16' IDENTIFIED BY '< Password >';
      - GRANT ALL PRIVILEGES ON *.* TO '< User >'@'172.27.0.0/16' WITH GRANT OPTION;
      - FLUSH PRIVILEGES;
   
   Postgres
   1. open the file: /etc/postgresql/<your_postgres_version>/main/postgresql.conf
      - add to listen_addresses = '172.27.0.1' via semicolon ( in case you left default Subnet value )
   2. in file: /etc/postgresql/<your_postgres_version>/main/pg_hba.conf
      - add a new entry: `host all all 172.27.0.0/16 md5`
   3. restart PostgreSQL
   
   There 172.27.0.0/16 could be replaced by your custom network.
   ```
3. **Dump Over SSH** - creates and downloads dump from the remote server. It connects by SSH to remote server and creates dump. Basically it looks like

   ```php
   sshpass -p 'passowrd' ssh user@0.0.0.1 -p 22 "mysqldump -uroot -hmysql -p12345 app" > local_dump.sql
   ```
4. **Manual -** This method requires only path to backup. Basically, it just take backup and imports it. All configuration should be done manually.
5. **SFTP -** Downloads backup from remote server by **SFTP/SSH** and imports it. It is different than **dump over ssh**  method cause it doesn't create backup.