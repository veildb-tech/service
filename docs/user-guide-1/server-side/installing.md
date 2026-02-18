# Installing

The Server could be installed in 2 ways:

## 1. Via install script

TODO: this is not finished

## 2. Manually with setup command

This way for cases when the customer directly downloads the source code.

Then he must execute the command:

```bash
./dbvisor-agent setup
```



## Steps after tool installing:

After successfully installing, need to execute are next commands:

```bash
dbvisor-agent app:server:add
```

Those commands will authorize the server in service.

The next one:

```bash
dbvisor-agent app:cron:install
```

Those commands will install required cron jobs, and the command will automatically execute on non-docker case
