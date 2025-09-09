# As Mais Tocadas de Tião Carreiro e Pardinho - Backend

API REST desenvolvida em Laravel para gerenciar um ranking das músicas mais populares da dupla Tião Carreiro e Pardinho.

## Instalação Rápida

### 1. Pré-requisitos

-   Docker Desktop instalado
-   Git instalado

### 2. Instalar e executar

```bash
# 1. Clonar o repositório
git clone `https://github.com/Diogo-Salvio/techp-back-laravel`
cd techp-back-laravel

# 2. Copiar arquivo de ambiente
copy .env.example .env

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
```

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

## Funcionalidades

- **Ranking Top 5**: Sistema de posicionamento das músicas mais visualizadas
- **Sugestões de Músicas**: Usuários podem sugerir novas músicas para o ranking
- **Sistema de Aprovação**: Administradores aprovam ou reprovam sugestões
- **Autenticação**: Sistema de login com Laravel Sanctum
- **Controle de Visualizações**: Contador de visualizações para cada música

## Tecnologias

- PHP 8.1+
- Laravel 10
- MySQL
- Laravel Sanctum (autenticação)
- Docker

## Estrutura do Projeto

- **Models**: `Musica`, `SugestaoMusica`, `User`
- **Controllers**: `MusicaController`, `SugestaoMusicaController`, `AuthController`
- **Middleware**: Autenticação e controle de admin
- **API REST**: Endpoints para CRUD de músicas e sugestões



## Endpoints Principais

### Públicos
- `GET /api/musicas` - Listar todas as músicas
- `GET /api/musicas/top5` - Top 5 músicas
- `POST /api/sugestoes` - Sugerir música
- `POST /api/login` - Login

### Admin (autenticação necessária)
- `GET /api/sugestoes/pendentes` - Ver sugestões pendentes
- `PATCH /api/sugestoes/{id}/aprovar` - Aprovar sugestão
- `PATCH /api/sugestoes/{id}/reprovar` - Reprovar sugestão
- `DELETE /api/musicas/{id}` - Remover música
- `POST /api/musicas/reorganizar-top5` - Reorganizar Top 5

## Credenciais Padrão

**Admin:**
- Email: fanumero1dotiaoecarreiro@admin.com
- Senha: boisoberano
