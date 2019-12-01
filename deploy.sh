#!/usr/bin/env bash

if hash composer 2>/dev/null; then
    COMPOSER_CMD=composer
else
    # for AWS Beanstalk
    COMPOSER_CMD=composer.phar
fi

cd web_app || exit 1
$COMPOSER_CMD install --no-dev --optimize-autoloader --no-interaction

exit 0
