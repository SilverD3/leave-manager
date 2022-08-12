# Leave Manager App

A simple web application to manage leaves and contracts.

## Installation

1. Clone the repository from GitHub

```bash
git clone https://github.com/SilverD3/leave-manager.git
```
2. Install the database

Database schema is located in `config/schema/db-script.sql`.

3. After installing the database, just start your web server.

If you have cloned the repository into your web server document root, just navigate to `localhost/leave-manager`. But if you have cloned the repository anywhere else, run following command from command line when you're in root directory:

```bash
php -S localhost:8090
```
Then navigate to `localhost:8090`.

## Configurations 

Configurations are available in `config` directory. Edit `config/app.php` to configure datasource and sessions.