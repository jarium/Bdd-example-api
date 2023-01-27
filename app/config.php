<?php
/*
|--------------------------------------------------------------------------
| Database Configuration
|--------------------------------------------------------------------------
*/
define('DBHOST', 'mariadb');
define('DBPORT', 3306);
define('DBNAME', 'bdd-example-api');
define('DBUSER', 'root');
define('DBPASS', 'root');

/*
|--------------------------------------------------------------------------
| Maintenance Configuration
|--------------------------------------------------------------------------
*/
define('MAINTENANCE', false); //=> Switch false with true for maintenance mode //Bakım modu için false yerine true yazın.

/*
|--------------------------------------------------------------------------
| Test Configuration
|--------------------------------------------------------------------------
*/

define ('TEST_ENV', true); //=> Switch false with true for test mode
define ('TEST_TIME', '2023-01-22 01:00:00');
