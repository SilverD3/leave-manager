# Leave Manager App

A simple web application to manage leaves and contracts.

## Prerequisites

To run this project, you need php 8.1 or latest and MySQL >= 5.7 or MariaDB >= 10.4 .

## Installation

1. Clone the repository from GitHub

```bash
git clone https://github.com/SilverD3/leave-manager.git
```
2. Install the database

Database schema is located in `config/schema/db-script.sql`. Create a database in DBMS (Database Management System) and use it to import this schema.

## Configurations 

Configurations are available in `config` directory. 

1. Edit `config/app.php` to configure datasource and sessions.
2. Set path `BASE_URL` in file `config/paths.php` to set the base URL
```php
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost:8090/');
}
```

## Run

From the command line, run following command in the root directory
```bash
php -S localhost:8090
```
Then navigate to `localhost:8090` in your browser.

## Example

- Clone the repository from Github:
```bash
git clone https://github.com/SilverD3/leave-manager.git
```
- Move into the root folder:
```bash
cd leave-manager
```
- Create database and import schema:
```bash
mysql > create database leave_manager;
mysql > source config/schema/leave_manager.sql;
```
- Edit datasource config in file `config/app.php`:
```php
'DataSource' => [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'leave_manager',
],
```
- Set the base url in file `config/paths.php`:
```php
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost:8090/');
}
```
- Start the server:

```bash
php -S localhost:8090
```
- Open your browser and type `localhost:8090` in the url bar.