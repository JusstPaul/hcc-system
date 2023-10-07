# QSign Comparator Ex

Instructions for developers.

## Requirements

### Installs

- Apache HTTP Server 2.4.41
- php 8.1 with the following extensions:
    - BCMath
    - Ctype
    - cURL
    - DOM
    - Fileinfo
    - JSON
    - Mbstring
    - OpenSSL
    - PCRE
    - PDO
    - Tokenizer
    - XML
- composer 2.3.5
- nodejs 16.14.2

### Services

- AWS S3 or any compatible file servers.

## Installation

Go to the project directory and run the following:

```
$ php artisan key:generate
$ composer install
$ npm i
$ cp .env.example .env
```

Modify the .env file to the preferred system configuration for:

- database
- file server
- redis cache

After configuring the file, run the following to migrate the database for the
first time:

```
php artisan migrate:fresh --seed
```

Then activate the local file storage:

```
php artisan storage:link
```

### Deployment

Use the following template for Apache:

```
<VirtualHost *:80>
    ServerAdmin sample-admin-email@mail.com
    ServerName sample-domain-name.com
    ServerAlias www.sample-domain-name.com
    DocumentRoot /var/www/hcc-system/public

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined

    <Directory /var/www/hcc-system>
        Require all granted
        AllowOverride All
        Options Indexes Multiviews FollowSymlinks
    </Directory>
</VirtualHost>

```

## Environment

### Development

Run the following to activate the development environment:

#### Server

```
php artisan serve
```

#### Client

```
npm run build
```

### Deployment

Run the following to build all the client JavaScript files:

```
npm run dev
```
