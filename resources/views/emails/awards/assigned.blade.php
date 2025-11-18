<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ви отримали нагороду</title>
    <style>
        body {
            background-color: #f3f4f6;
            color: #111827;
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .wrapper {
            width: 100%;
            padding: 32px 0;
        }
        .container {
            max-width: 640px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 35px rgba(15, 23, 42, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #14b8a6, #3b82f6);
            color: #ffffff;
            padding: 32px 40px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 12px;
            font-size: 28px;
            font-weight: 700;
        }
        .header p {
            margin: 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 40px;
        }
        .badge-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 28px;
            text-align: center;
        }
        .badge-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
            background: #1f2937;
            color: #ffffff;
            font-size: 40px;
            font-weight: 700;
        }
        .badge-title {
            font-size: 22px;
            margin-bottom: 12px;
            font-weight: 700;
            color: #111827;
        }
        .badge-description {
            font-size: 16px;
            color: #4b5563;
            margin-bottom: 18px;
            line-height: 1.6;
        }
        .meta {
            margin-top: 24px;
            font-size: 14px;
            color: #6b7280;
        }
        .note-box {
            margin-top: 26px;
            padding: 20px;
            border-left: 4px solid #6366f1;
            background: #eef2ff;
            color: #312e81;
            border-radius: 10px;
            font-size: 15px;
            line-height: 1.6;
        }
        .cta {
            display: inline-block;
            margin-top: 32px;
            padding: 14px 28px;
            background: linear-gradient(135deg, #6366f1, #3b82f6);
            color: #ffffff;
            border-radius: 9999px;
            text-decoration: none;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .footer {
            padding: 28px 40px 36px;
            text-align: center;
            font-size: 13px;
            color: #9ca3af;
            background: #f9fafb;
        }
        @media (max-width: 640px) {
            .content {
                padding: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>Вітаємо, {{ $user->name ?? $user->username ?? 'книжковий друже' }}!</h1>
                <p>Ви щойно отримали нову нагороду на Books Foroom.</p>
            </div>
            <div class="content">
                <div class="badge-card">
                    <div class="badge-icon" style="background: {{ $award->color ?? '#1f2937' }};">
                        {{ mb_substr($award->name, 0, 1) }}
                    </div>
                    <div class="badge-title">{{ $award->name }}</div>
                    @if($award->description)
                        <div class="badge-description">{{ $award->description }}</div>
                    @endif
                    <div class="meta">
                        Нагорода призначена: {{ $awardedAt->timezone(config('app.timezone'))->format('d.m.Y H:i') }}<br>
                    </div>
                    @if(!empty($note))
                        <div class="note-box">
                            <strong>Коментар від модератора:</strong><br>
                            {{ $note }}
                        </div>
                    @endif
                    <a href="{{ url('/') }}" class="cta">Перейти на сайт</a>
                </div>
            </div>
            <div class="footer">
                Ви отримали цей лист, тому що є учасником спільноти Books Foroom.<br>
                Якщо ви не очікували цей лист, просто проігноруйте його.
            </div>
        </div>
    </div>
</body>
</html>
