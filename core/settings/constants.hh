<?hh

if(!defined('SITE_DOMAIN')) {
    if(isset($_SERVER) && isset($_SERVER['HTTP_HOST'])) {
        define('SITE_DOMAIN', $_SERVER['HTTP_HOST']);
    } else {
        define('SITE_DOMAIN', 'localhost');
    }
}

if(!defined('SITE_NAME')) {
    define('SITE_NAME', 'HydraCore');
}

if(!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'DEV');
}

if(!defined('PROTOCOL')) {
    if(isset($_SERVER) && isset($_SERVER['SERVER_PROTOCOL'])) {
        define('PROTOCOL', $_SERVER['SERVER_PROTOCOL']);
    } else {
        define('PROTOCOL', 'http');
    }
}

if(!defined('AUTHOR')) {
    define('AUTHOR', 'Ryan Howell');
}

if(!defined('ERROR_LOGGING')) {
    define('ERROR_LOGGING', 'ALL');
}

if(!defined('REGISTER_SHUTDOWN')) {
    define('REGISTER_SHUTDOWN', true);
}

if(!defined('MODE')) {
    define('MODE', 'MVC');
}

if(!defined('TIMEZONE')) {
    define('TIMEZONE', 'Europe/London');
}

if(!defined('ENCODING')) {
    define('ENCODING', 'UTF-8');
}


if(!defined('DB_ENCODING')) {
    define('DB_ENCODING', 'utf8mb4');
}
