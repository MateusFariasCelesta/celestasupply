release: composer install && php artisan migrate --force
web: php -S 0.0.0.0:${PORT:-8000} -t public/
worker: php artisan queue:work --tries=3 --timeout=90