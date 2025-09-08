# 🎵 As Mais Tocadas de Tião Carreiro e Pardinho - BackEnd



## 🚀 Instalação Rápida

### 1. Pré-requisitos
- Docker Desktop instalado
- Git instalado

### 2. Instalar e executar
```bash
# 1. Clonar o repositório
git clone [URL_DO_REPOSITORIO]
cd laravel-backend

# 2. Iniciar aplicação (Windows)
docker-start.bat

# 2. Iniciar aplicação (Linux/Mac)
docker-compose up -d
```

### 3. Acessar aplicação
- **API:** http://localhost:8000/api
- **phpMyAdmin:** http://localhost:8080

## 🔑 Credenciais

### Usuário Admin
- **Email:** fanumero1dotiaoecarreiro@admin.com
- **Senha:** boisoberano

### Banco de Dados
- **Host:** localhost:3306
- **Database:** laravel_db
- **Username:** laravel_user
- **Password:** laravel_password

### phpMyAdmin
- **URL:** http://localhost:8080
- **Username:** root
- **Password:** root_password

## 🛠️ Comandos Úteis

### Parar aplicação
```bash
# Windows
docker-stop.bat

# Linux/Mac
docker-compose down
```

### Ver logs
```bash
# Windows
docker-logs.bat

# Linux/Mac
docker-compose logs -f app
```

### Executar comandos Artisan
```bash
# Windows
docker-artisan.bat migrate
docker-artisan.bat "migrate:fresh --seed"

# Linux/Mac
docker-compose exec app php artisan migrate
```

## 🐛 Problemas Comuns

### Porta já em uso
```bash
# Verificar processos
netstat -ano | findstr :8000
netstat -ano | findstr :3306

# Parar processo
taskkill /PID [PID_NUMBER] /F
```

### Rebuild da aplicação
```bash
docker-compose build --no-cache
docker-compose up -d
```

## 📋 Endpoints da API

### Públicos
- `GET /api/musicas` - Listar músicas
- `GET /api/musicas/top5` - Top 5 músicas
- `POST /api/sugestoes` - Sugerir música
- `POST /api/login` - Login

### Admin (requer autenticação)
- `GET /api/sugestoes/pendentes` - Ver sugestões pendentes
- `PATCH /api/sugestoes/{id}/aprovar` - Aprovar sugestão
- `PATCH /api/sugestoes/{id}/reprovar` - Reprovar sugestão
- `DELETE /api/musicas/{id}` - Remover música
- `PATCH /api/musicas/{id}/posicao` - Atualizar posição
- `POST /api/musicas/reorganizar-top5` - Reorganizar Top 5
