<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Código de Verificação</title>
</head>
<body style="font-family: sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 480px; margin: 0 auto; background: #fff; border-radius: 8px; padding: 32px;">
        <h2 style="margin-top: 0;">Verificação de Email</h2>
        <p>Use o código abaixo para concluir seu cadastro. Ele expira em <strong>10 minutos</strong>.</p>
        <div style="font-size: 36px; font-weight: bold; letter-spacing: 8px; text-align: center; padding: 24px 0;">
            {{ $code }}
        </div>
        <p style="color: #888; font-size: 13px;">Se você não solicitou este código, ignore este email.</p>
    </div>
</body>
</html>
