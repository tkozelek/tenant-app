#!/bin/bash

set -e

cd /home/site/wwwroot

if [ -f artisan ]; then
    php artisan config:clear || true
    php artisan route:clear || true
    php artisan view:clear || true

    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

cp /home/site/wwwroot/nginx.conf /etc/nginx/sites-available/default 2>/dev/null || true

service nginx reload || true
