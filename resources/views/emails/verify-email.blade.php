<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Підтвердження електронної пошти</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
        h1 {
            color: #2d3748;
            margin: 0;
            font-size: 28px;
        }
        .content {
            margin-bottom: 30px;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .button:hover {
            transform: translateY(-2px);
        }
        .footer {
            text-align: center;
            color: #718096;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        .warning {
            background: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            color: #c53030;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">📚</div>
            <h1>Книжковий форум</h1>
        </div>

        <div class="content">
            <h2>Привіт, {{ $user->first_name }}!</h2>
            
            <p>Дякуємо за реєстрацію на нашому книжковому форумі! Для завершення реєстрації та активації вашого акаунту, будь ласка, підтвердіть свою електронну пошту.</p>

            <p>Натисніть кнопку нижче, щоб підтвердити свою електронну адресу:</p>

            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="button">
                    Підтвердити електронну пошту
                </a>
            </div>

            <div class="warning">
                <strong>Увага:</strong> Якщо ви не реєструвалися на нашому сайті, просто проігноруйте цей лист.
            </div>

            <p>Якщо кнопка не працює, скопіюйте та вставте це посилання у ваш браузер:</p>
            <p style="word-break: break-all; background: #f7fafc; padding: 10px; border-radius: 4px; font-family: monospace;">
                {{ $verificationUrl }}
            </p>
        </div>

        <div class="footer">
            <p>Цей лист було надіслано автоматично. Будь ласка, не відповідайте на нього.</p>
            <p>&copy; {{ date('Y') }} Книжковий форум. Всі права захищені.</p>
        </div>
    </div>
</body>
</html>
