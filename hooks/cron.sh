#!/bin/bash
cd "$(dirname "$0")"

hhvm -v >/dev/null 2>&1 || {
    hhvm cron.php
    exit 0;
}

php5 -v >/dev/null 2>&1 || {
    php5 cron.php
    exit 0;
}

php -v >/dev/null 2>&1 || {
    php cron.php
    exit 0;
}

exit 1;