#!/bin/sh

echo "Esperando a que PostgreSQL esté listo..."
sleep 5

echo "Ejecutando migraciones..."
php artisan migrate --force

echo "Ejecutando seeders..."
php artisan db:seed --class=DatabaseSeeder --force

echo "Limpiando cache..."
php artisan config:cache
php artisan route:cache

echo "Iniciando Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf