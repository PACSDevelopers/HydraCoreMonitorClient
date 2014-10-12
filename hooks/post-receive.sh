#!/bin/bash
cd "$(dirname "$0")"

hhvm -v >/dev/null 2>&1 || {
    hhvm post-receive.php
    exit 0;
}

php5 -v >/dev/null 2>&1 || {
    php5 post-receive.php
    exit 0;
}

php -v >/dev/null 2>&1 || {
    php post-receive.php
    exit 0;
}

exit 1;