@echo off
echo 🐳 Iniciando aplicação Laravel com Docker...
echo.

REM Copiar arquivo de ambiente se não existir
if not exist .env (
    echo 📋 Copiando arquivo de ambiente...
    copy env.docker.example .env
    echo ✅ Arquivo .env criado!
    echo.
)

REM Gerar chave da aplicação
echo 🔑 Gerando chave da aplicação...
docker-compose exec app php artisan key:generate

echo.
echo 🚀 Iniciando containers...
docker-compose up -d

echo.
echo ⏳ Aguardando banco de dados...
timeout /t 10 /nobreak > nul

echo.
echo 🗄️ Executando migrations...
docker-compose exec app php artisan migrate --force

echo.
echo 🌱 Executando seeders...
docker-compose exec app php artisan db:seed --class=AdminUserSeeder --force

echo.
echo ✅ Aplicação iniciada com sucesso!
echo.
echo 🌐 Aplicação: http://localhost:8000
echo 🗄️ phpMyAdmin: http://localhost:8080
echo.
echo 📋 Credenciais do banco:
echo    Host: localhost:3306
echo    Database: laravel_db
echo    Username: laravel_user
echo    Password: laravel_password
echo.
echo 👤 Usuário Admin:
echo    Email: fanumero1dotiaoecarreiro@admin.com
echo    Senha: boisoberano
echo.
pause
