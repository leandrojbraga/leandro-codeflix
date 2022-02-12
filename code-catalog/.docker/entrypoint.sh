#!/bin/bash

#On error no such file entrypoint.sh, execute in terminal - dos2unix .docker\entrypoint.sh
### CONFIG AND START FRONTEND
npm config set cache /var/www/.npm-cache --global
cd /var/www/frontend && npm install && cd ..

### CONFIG AND START BACKEND
cd backend
if [ ! -f ".env" ]; then
    cp .env.example .env
fi
if [ ! -f ".env.testing" ]; then
    cp .env.testing.example .env.testing
fi
composer install
php artisan key:generate
php artisan migrate
php artisan storage:link

php-fpm
