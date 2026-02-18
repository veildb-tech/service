# Installation

## Installation

## Service Installation

At the beginning, it is required to install the service at [https://github.com/dbvisor-pro/service](https://github.com/dbvisor-pro/service).

It can be done easily by executing `install.sh` a script.

Then you must create an account.

## **Server Side Configuration**

### Preparing environment

Ensure that all the necessary software is installed before proceeding with the installation.

**Requirements**:

* Docker
* curl
* lsof

1. To install Docker, refer to the official documentation: [Docker Installation Guide](https://docs.docker.com/engine/install/).
2.  For curl and lsof installation, execute the following commands based on your operating system:

    ```bash
    ## For debian
    sudo apt update && sudo apt install curl lsof

    ## For alpine
    sudo apk add curl lsof
    ```
3.  The next step involves installing the DBvisor Agent. Execute the following command:

    ```bash
    ## For alpine
    curl http://db-manager-cli.bridge2.digital/download/dbvisor-agent-install | sh

    ## For debian
    curl http://db-manager-cli.bridge2.digital/download/dbvisor-agent-install | bash
    source ~/.bashrc
    ```

{% hint style="warning" %}
**Important Note:** During the installation, you will be prompted for Docker installation. It is strongly recommended to use Docker. Non-Docker installation requires additional configurations on your end
{% endhint %}

### Configurations

1.  Add new server:

    ```bash
    dbvisor-agent app:server:add
    ```
2. Enter your email, password and workspace code.
3. Enter server name

After you add a new server, you can set up a new Database. Go to [Database Management](../dbvisor-agent/database-management.md) to get more information on how to add and manage your databases.

4. Also, you can configure access to

## Client side

The main requirements for this tool is PHP 8. It can be installed using the following command:

```bash
sudo apt update && apt install php
```

Execute the following command to install the client-side version.

```bash
curl http://db-manager-cli.bridge2.digital/download/install | bash
```

After it is installed locally, you have to log in and enterthe  public key provided by your administrator (the person who has access to dbvisor agent / to the server):

* [How to generate keypair](../dbvisor-agent/generate-key-pairs.md)
* [How to save locally public key](../dbvisor-client/save-public-key.md)
