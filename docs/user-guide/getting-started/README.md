# Getting Started

## Getting Started

## **Overview**

This application serves several key purposes:

1. **Protect Your Client's Data:** The application enables the removal of sensitive information, such as customer addresses, emails, and billing records, from databases. Not only does it cleanse the data, but it also substitutes it with predefined patterns. This enables developers to work with realistic data sizes while safeguarding against data leaks. Additionally, database access can be restricted by user groups.
2. **Ease of Sharing:** Developers can effortlessly keep their databases up to date, saving time on tasks such as sharing and backups. Users can configure the data-cleaning frequency, and updated databases can be downloaded as needed.
3. **Versatile Usage:** In addition to the aforementioned features, this application is valuable for preparing your application or website for demos, presentations, and more.

The primary advantage is the ability to use a single interface across various servers.

## Architecture

From a technical standpoint, the system consists of three applications:

1. **DBvisor Service:** The primary website where users interact. It allows configuring rules, accessing database configurations, and viewing important logs.
2. **DBvisor Agent:** This application is installed on your server and is responsible for processing and backing up databases.
3. **DBvisor Client:** Installed locally on developers' computers, this application simplifies logging in and downloading the latest backup.

{% hint style="warning" %}
**Important Note:** All database credentials and data are stored on your server side. The service side exclusively retains database schemas and the server's IP address
{% endhint %}
