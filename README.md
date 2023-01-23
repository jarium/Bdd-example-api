# Api Example For BDD
Example api project for BDD, written as mvc and in pure php.

## Installation
```bash
$ cp .env.example .env
$ docker-compose up -d
```
After containers are created, install dependencies via composer.

```bash
$ docker exec -it bdd-example-api-php-1 /bin/bash
$ composer install
```

### Setting Up The MySQL Database
After successfully installing, go to http://localhost:8082/ then login with the info below:

- Server: `mariadb`
- Username: `root`
- Password: `root`

After logging in, create a database called `bdd-example-api` then import the `/app/features/fixtures/init.sql` file to adminer.
After a successful import, the database will be ready.
<br> <br>

After installing the docker container, dependencies and setting up our mysql database, the api service will be available at http://localhost

## Behat Tests
```bash
$ docker exec -it bdd-example-api-php-1 /bin/bash
$ vendor/bin/behat -f pretty
```

## Maintenance
You can set/unset the maintenance mode by changing the MAINTENANCE constant in the "config.php" file. False = no maintenance, True = maintenance. If set to true, all the users that use the api will get the maintenance response and all the routes will be unavailable other than the route that returns maintenance response.

## For More
You can mail to: efebyk97@gmail.com for more. <br>
With my regards, <br>
Efe Buyuk
