#!/bin/sh

echo "Esperando a que PostgreSQL esté listo..."
sleep 10

echo "Verificando si las migraciones ya se ejecutaron..."
if [ ! -f /var/www/html/.migrations_done ]; then
    echo "Ejecutando migraciones..."
    php artisan migrate --force
    
    echo "Ejecutando seeders..."
    php artisan db:seed --class=DatabaseSeeder --force
    
    echo "Limpiando cache..."
    php artisan config:cache
    php artisan route:cache
    
    # Marcar que las migraciones se completaron
    touch /var/www/html/.migrations_done
    echo "✓ Migraciones y seeders completados"
else
    echo "Las migraciones ya se ejecutaron anteriormente"
fi

echo "Iniciando Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
