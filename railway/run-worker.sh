#!/bin/bash
# Liga o worker em segundo plano (&)
php artisan queue:work --vhost=/ &

# Inicia o servidor web principal (substitua pelo comando padrão do seu app)
# Se estiver usando o padrão do Railway (Nixpacks), geralmente é:
php artisan serve --host 0.0.0.0 --port ${PORT:-8080}