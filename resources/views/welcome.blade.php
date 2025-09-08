<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>As Mais Tocadas de Tião Carreiro e Pardinho - Backend</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #dc3545;
            text-align: center;
            margin-bottom: 30px;
        }

        .welcome-text {
            text-align: center;
            margin-bottom: 40px;
            font-size: 18px;
            color: #666;
        }

        .routes-section {
            margin-top: 30px;
        }

        .routes-section h2 {
            color: #333;
            border-bottom: 2px solid #dc3545;
            padding-bottom: 10px;
        }

        .route-item {
            background: #f8f9fa;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            border-left: 4px solid #dc3545;
        }

        .method {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 12px;
            margin-right: 10px;
        }

        .get {
            background: #28a745;
            color: white;
        }

        .post {
            background: #007bff;
            color: white;
        }

        .endpoint {
            font-family: monospace;
            font-weight: bold;
        }

        .description {
            color: #666;
            margin-top: 5px;
        }

        .api-link {
            text-align: center;
            margin-top: 30px;
        }

        .api-link a {
            display: inline-block;
            background: #dc3545;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .api-link a:hover {
            background: #c82333;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>As Mais Tocadas de Tião Carreiro e Pardinho</h1>

        <div class="welcome-text">
            Bem-vindo ao backend da API!<br>
            Aqui você pode acessar todas as funcionalidades do sistema de ranking musical.
        </div>

        <div class="routes-section">
            <h2>Rotas Públicas da API</h2>

            <div class="route-item">
                <span class="method get">GET</span>
                <span class="endpoint">/api/musicas</span>
                <div class="description">Listar todas as músicas cadastradas</div>
            </div>

            <div class="route-item">
                <span class="method get">GET</span>
                <span class="endpoint">/api/musicas/top5</span>
                <div class="description">Listar as 5 músicas mais populares</div>
            </div>

            <div class="route-item">
                <span class="method post">POST</span>
                <span class="endpoint">/api/sugestoes</span>
                <div class="description">Sugerir uma nova música para o ranking</div>
            </div>

            <div class="route-item">
                <span class="method post">POST</span>
                <span class="endpoint">/api/login</span>
                <div class="description">Fazer login no sistema (para administradores)</div>
            </div>
        </div>

        <div class="api-link">
            <a href="/api">Acessar Documentação da API</a>
        </div>
    </div>
</body>

</html>