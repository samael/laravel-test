<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css" />
    <style>
        html, body {
            margin: 0;
            padding: 0;
            background: #f4f7fb;
            font-family: "Segoe UI", Tahoma, sans-serif;
        }

        .topbar {
            background: linear-gradient(90deg, #124c7a, #1d7db5);
            color: #fff;
            padding: 12px 16px;
            font-size: 14px;
        }

        .topbar strong {
            font-size: 15px;
        }

        #swagger-ui {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
        }
    </style>
</head>
<body>
<div class="topbar">
    <strong>Laravel Test API Docs</strong> - Swagger UI
</div>
<div id="swagger-ui"></div>
<script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
<script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-standalone-preset.js"></script>
<script>
    window.ui = SwaggerUIBundle({
        url: "{{ asset('docs/openapi.yaml') }}",
        dom_id: '#swagger-ui',
        deepLinking: true,
        presets: [
            SwaggerUIBundle.presets.apis,
            SwaggerUIStandalonePreset,
        ],
        layout: 'BaseLayout',
    });
</script>
</body>
</html>
