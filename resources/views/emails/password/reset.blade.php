<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Відновлення пароля</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f3f4f6;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 40px 20px;">
                <table role="presentation" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 40px 40px 20px; text-align: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px 12px 0 0;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: bold;">
                                Відновлення пароля
                            </h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 30px 40px;">
                            <p style="margin: 0 0 20px; color: #374151; font-size: 16px; line-height: 1.6;">
                                Вітаємо, <strong>{{ $user->name ?? $user->username }}</strong>!
                            </p>

                            <p style="margin: 0 0 20px; color: #374151; font-size: 16px; line-height: 1.6;">
                                Ви отримали цей лист, тому що ми отримали запит на відновлення пароля для вашого акаунту.
                            </p>

                            <p style="margin: 0 0 30px; color: #374151; font-size: 16px; line-height: 1.6;">
                                Натисніть кнопку нижче, щоб відновити пароль:
                            </p>

                            <!-- Button -->
                            <table role="presentation" style="width: 100%; margin: 30px 0;">
                                <tr>
                                    <td style="text-align: center;">
                                        <a href="{{ $url }}" 
                                           style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 6px rgba(102, 126, 234, 0.3);">
                                            Відновити пароль
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 30px 0 20px; color: #6b7280; font-size: 14px; line-height: 1.6;">
                                Або скопіюйте та вставте це посилання у ваш браузер:
                            </p>
                            <p style="margin: 0 0 30px; padding: 12px; background-color: #f3f4f6; border-radius: 6px; word-break: break-all; color: #374151; font-size: 14px; font-family: monospace;">
                                {{ $url }}
                            </p>

                            <p style="margin: 0 0 20px; color: #6b7280; font-size: 14px; line-height: 1.6;">
                                Це посилання дійсне протягом <strong>{{ $count }} хвилин</strong>.
                            </p>

                            <p style="margin: 0; color: #6b7280; font-size: 14px; line-height: 1.6;">
                                Якщо ви не запитували відновлення пароля, просто проігноруйте цей лист. Ваш пароль залишиться без змін.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 40px; background-color: #f9fafb; border-radius: 0 0 12px 12px; text-align: center;">
                            <p style="margin: 0; color: #6b7280; font-size: 12px; line-height: 1.5;">
                                З повагою,<br>
                                <strong style="color: #374151;">Команда Книжкового форуму</strong>
                            </p>
                            <p style="margin: 15px 0 0; color: #9ca3af; font-size: 11px;">
                                Це автоматичне повідомлення, будь ласка, не відповідайте на цей лист.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

