# As Mais Tocadas de Tião Carreiro e Pardinho - Backend

API REST desenvolvida em Laravel para gerenciar um ranking das músicas mais populares da dupla Tião Carreiro e Pardinho.



## Instalação

Para instruções detalhadas de instalação e configuração, consulte o arquivo [README-Docker.md](README-Docker.md).

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
