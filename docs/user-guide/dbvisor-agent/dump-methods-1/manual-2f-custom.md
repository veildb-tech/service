# Manual / Custom

The primary concept of the manual backup method is that the administrator should configure the creation of backups. This can be achieved through various means such as using crontab, synchronization from another service, or incorporating it into a CI/CD pipeline.

One crucial consideration is to store these backups inside the `<path_to_local_backups>` folder, where `<path_to_local_backups>` is specified during the installation process. You can modify this path in the .env file located at `$HOME/.dbvisor-agent/.env`. Change the variable `APP_DOCKER_LOCAL_BACKUPS_PATH=` accordingly.

{% hint style="warning" %}
When setting up a new database using the manual method, the path to the file should start from `<path_to_local_backups>.` For example:

```sql
/root/dbvisor-agent-backups/local_backups/my_project/backup.sql 
```

Here,`<path_to_local_backups>` is assumed to be `/root/dbvisor-agent-backups/local_backups/`.
{% endhint %}
