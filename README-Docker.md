# As Mais Tocadas de Tião Carreiro e Pardinho - BackEnd

## Instalação Rápida

### 1. Pré-requisitos

-   Docker Desktop instalado
-   Git instalado

### 2. Instalar e executar

bash
# 1. Clonar o repositório
git clone [URL_DO_REPOSITORIO]
cd [REPOSITORIO]

# 2. Copiar arquivo de ambiente
copy env.docker.example .env

# 3. Build e iniciar aplicação
docker-compose build --no-cache
docker-compose up -d

# 4. Aguardar containers iniciarem (30 segundos)
# 5. Gerar chave de aplicação
docker-compose exec app php artisan key:generate 

# 6. Executar migrations
docker-compose exec app php artisan migrate 

# 7. Executar seeders (usuário admin + músicas)
docker-compose exec app php artisan db:seed


### 3. Acessar aplicação

-   *API:* http://localhost:8000/api
-   *phpMyAdmin:* http://localhost:8080

## Credenciais

### Usuário Admin

-   *Email:* fanumero1dotiaoecarreiro@admin.com
-   *Senha:* boisoberano

### Banco de Dados

-   *Host:* localhost:3306
-   *Database:* laravel_db
-   *Username:* laravel_user
-   *Password:* laravel_password

### phpMyAdmin

-   *URL:* http://localhost:8080
-   *Username:* root
-   *Password:* root_password

## Comandos Úteis

### Parar aplicação

docker-compose down



### Executar comandos Artisan

bash
# Windows
docker-artisan.bat migrate
docker-artisan.bat "migrate:fresh --seed"

# Linux/Mac
docker-compose exec app php artisan migrate


## Problemas Comuns

### Porta já em uso

bash
# Verificar processos
netstat -ano | findstr :8000
netstat -ano | findstr :3306

# Parar processo
taskkill /PID [PID_NUMBER] /F


### Rebuild da aplicação

bash
docker-compose build --no-cache
docker-compose up -d


### Verificar status dos containers

bash
docker-compose ps


## Endpoints da API

### Públicos

-   GET /api/musicas - Listar músicas
-   GET /api/musicas/top5 - Top 5 músicas
-   POST /api/sugestoes - Sugerir música
-   POST /api/login - Login

### Admin (requer autenticação)

-   GET /api/sugestoes/pendentes - Ver sugestões pendentes
-   PATCH /api/sugestoes/{id}/aprovar - Aprovar sugestão
-   PATCH /api/sugestoes/{id}/reprovar - Reprovar sugestão
-   DELETE /api/musicas/{id} - Remover música
-   PATCH /api/musicas/{id}/posicao - Atualizar posição
-   POST /api/musicas/reorganizar-top5 - Reorganizar Top 5