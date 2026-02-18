# Database Management

## Database Management

## **Adding a New Database**

To add a new database, follow these steps:

Run the following command:

```bash
dbvisor-agent app:db:add
```

It will ask for all the required information and analyze the database. Let's dive deeper with the required information:

2. The command will prompt you for the necessary information and analyze the database. Let's delve into the required details:
   * **Database Name:** Provide a regular identifier for the database.
   * **Engine:** Choose the database engine used; currently supporting MySQL and PostgreSQL.
   * **Platform:** Some projects may have a specific database structure. We support certain CMS systems for easy management. For instance, Magento 2 involves additional processing of attributes. If your platform is not listed, select "custom" to manually configure rules on the service side.
   * **Dump Methods:** Select where the original database backup is stored. For more details, refer to this link: [Dump Methods](dump-methods.md).
3. Based on the selected dump method, provide all necessary information and credentials. Note: We do not store these credentials on the service side; they are stored in the dbvisor-agent config files. The responsibility to protect these configs lies with the server administrator.
4. After filling in all the information, the system will automatically check the connection to the database. If the connection is established, it will prompt you to analyze the database. We recommend performing the analysis (duration depends on the database size) to configure rules effectively.

## **Analyzing the Database**

The analysis of the database occurs automatically each time the database processes. However, if you wish to analyze it manually, you can use the following command

```bash
dbvisor-agent app:db:analyze --uid=<DB_UUID>
```

Replace **\<DB\_UUID>** with the unique identifier of your database.

This command retrieves or creates a backup from your source, imports it into a temporary database, and executes the analysis. Once the analysis is complete, the temporary database is dropped.

{% hint style="info" %}
For a more detailed understanding of how we process databases, you can refer to the following link: [General Principles](../getting-started/general-principies.md).
{% endhint %}

## Process Database

To start processing of database need to execute following command:

```bash
dbvisor-agent app:db:process
```

This command sends a request to the service to retrieve the scheduled database and initiates the processing. If there are no scheduled databases, the command will skip the process.

{% hint style="info" %}
For a more detailed understanding of how we process databases, you can refer to the following link: [General Principles](../getting-started/general-principies.md).
{% endhint %}
