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

if(!defined('APP_VERSION')) {
    define('APP_VERSION', '0.0.1');
}

if(!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'DEV');
}

if(!defined('PROTOCOL')) {
    if(isset($_SERVER) && isset($_SERVER['HTTPS'])) {
        define('PROTOCOL', 'https');
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

if(!defined('ALLOW_ERRORS')) {
    define('ALLOW_ERRORS', false);
}

if(!defined('ERROR_ADDRESS')) {
    define('ERROR_ADDRESS', false);
}

if(!defined('ERROR_ALERTS')) {
    define('ERROR_ALERTS', false);
}

if(!defined('REGISTER_SHUTDOWN')) {
    define('REGISTER_SHUTDOWN', true);
}

if(!defined('MODE')) {
    define('MODE', 'MVC');
}

if(!defined('TIMEZONE')) {
    define('TIMEZONE', 'Europe/London');
    date_default_timezone_set('Europe/London');
} else {
    date_default_timezone_set(TIMEZONE);
}

if(!defined('TIMEZONE_OFFSET')) {
    $utctimezone = new DateTimeZone('UTC');
    $timezone = new DateTimeZone(TIMEZONE);
    $offset = $timezone->getOffset(new DateTime(NULL, $utctimezone));
    $offsetHours = round(abs($offset)/3600);
    $offsetMinutes = round((abs($offset) - $offsetHours * 3600) / 60);
    $offsetString = ($offset < 0 ? '-' : '+')
        . ($offsetHours < 10 ? '0' : '') . $offsetHours
        . ':'
        . ($offsetMinutes < 10 ? '0' : '') . $offsetMinutes;
    define('TIMEZONE_OFFSET', $offsetString);
    $timezone = null;
    unset($timezone);
}

if(!defined('ENCODING')) {
    define('ENCODING', 'UTF-8');
}


if(!defined('DB_ENCODING')) {
    define('DB_ENCODING', 'utf8mb4');
}

if(!defined('LOGIN_PAGE')) {
    define('LOGIN_PAGE', '');
}


