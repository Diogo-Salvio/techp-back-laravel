@echo off
if "%1"=="" (
    echo ❌ Uso: docker-artisan.bat [comando]
    echo.
    echo Exemplos:
    echo   docker-artisan.bat migrate
    echo   docker-artisan.bat "migrate:fresh --seed"
    echo   docker-artisan.bat "db:seed --class=AdminUserSeeder"
    echo   docker-artisan.bat tinker
    echo.
    pause
    exit /b 1
)

echo 🚀 Executando: php artisan %1
echo.

docker-compose exec app php artisan %*

echo.
pause
