# Generate key-pairs

To generate keypair need to execute following command:

```bash
dbvisor-agent app:server:generate-keypair --identifier=email@example.com
```

Where `--identifier` is unique ID of user (it could be email, name etc.)

After command is executed it will provide public key that should be provided to the developer. After that developer should save this key using dbvisor-client.