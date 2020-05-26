# Supermetrics code assignment

![Supermetrics logo](https://financesonline.com/uploads/2017/05/supermetrics.png "Supermetrics")

## Describtion

Implementation of a simple API for fetching users posts statistics without using any existing PHP framework.

### Environments

To start this app you need to have:

1. [Apache](https://httpd.apache.org/download.cgi) server (v.2.4.38)
1. [MySQL](https://www.mysql.com/downloads/) server (v.5.7)
1. [PHP](https://www.php.net/downloads.php) (v.7.4.6)
1. Installed [pdo extension](https://www.php.net/manual/en/pdo.installation.php) for PHP

_Or_:
1. [Docker](https://www.docker.com/get-started) engine (v.19.03.X)
1. [docker-compose](https://docs.docker.com/compose/install/) (v.1.25.X)

### Set up (Part 1)
1. Run `cp .env.example .env` for copying all needed environment variables

__For non-Docker users:__
2. Start mysql server `service mysql start`
3. Create a database with props from `.env`
3. Copy content of `./migrations/supermetrics_api_user.sql` and run in SQL console
4. Copy content of `./app` into `<APACHE_FOLDER>/www/html`
5. Enable rewrite engine with `a2enmod rewrite` and restart apache `sudo systemctl restart apache2` 
6. Load env variables from `.env` with:
```bash
if [ -f .env ]
then
  export $(cat .env | sed 's/#.*//g' | xargs)
fi
```

__For Docker users:__
2. Build app image with `docker-compose build`

### Start app
__For non-Docker users:__
1. Start Apache server `sudo systemctl start apache2`

__For Docker users:__
1. Start app `docker-compose up` _(this command automatically runs needed migrations on the first start and so on)_

### Stop app
__For non-Docker users:__
1. Stop Apache server `sudo systemctl stop apache2`
2. Stop MySQL server `service mysql stop`

__For Docker users:__
1. `docker-compose stop` or `docker-compose down` _(if you want to remove created containers/networks/etc)_

_Note: MySQL data stored under `./tmp/db` so you need to remove this folder if you want completely remove mysql container with its data._

### App REST API documentation

You can import app REST API documentation to your Postman app from `./docs/Supermetrics Code Assignment.postman_collection.json`