<?php

namespace App\Helpers;

class HtmlSanitizer
{
    /**
     * Разрешенные теги: только параграф, жирный, курсив, подчеркивание, маленький, перенос строки, списки, ссылки
     * Разрешены: p, br, strong, b, em, i, u, small, ul, ol, li, a
     * Изображения (img) и другие теги удаляются для безопасности
     */
    private static $allowedTags = '<p><br><strong><b><em><i><u><small><ul><ol><li><a>';

    /**
     * Очищает HTML от опасных тегов и атрибутов
     * 
     * @param string|null $html
     * @return string
     */
    public static function sanitize($html)
    {
        if (empty($html)) {
            return $html;
        }

        // Удаляем все теги кроме разрешенных
        $html = strip_tags($html, self::$allowedTags);

        // Удаляем все атрибуты style и event handlers (onclick, onload и т.д.)
        $html = preg_replace('/\s*style\s*=\s*["\'][^"\']*["\']/i', '', $html);
        $html = preg_replace('/\s*on\w+\s*=\s*["\'][^"\']*["\']/i', '', $html);

        // Удаляем все script и style теги (на случай если они остались)
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i', '', $html);
        $html = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/i', '', $html);

        // Валидация и очистка ссылок - оставляем только href без опасных протоколов
        $html = preg_replace_callback('/<a\s+([^>]*)>(.*?)<\/a>/is', function($matches) {
            $attrs = $matches[1];
            $text = $matches[2];
            
            // Извлекаем href
            if (preg_match('/href\s*=\s*["\']([^"\']*)["\']/i', $attrs, $hrefMatch)) {
                $href = $hrefMatch[1];
                // Разрешаем только http, https или относительные URL
                if (preg_match('/^(https?:\/\/|\/)/i', $href)) {
                    return '<a href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '">' . $text . '</a>';
                }
            }
            // Если href невалиден, возвращаем только текст
            return $text;
        }, $html);

        // Удаляем все остальные атрибуты из тегов (оставляем только разрешенные выше)
        // Для тегов p, br, strong, b, em, i, u, small, ul, ol, li - убираем все атрибуты
        $html = preg_replace('/<(p|br|strong|b|em|i|u|small|ul|ol|li)(\s[^>]*)?>/i', '<$1>', $html);

        // Удаляем закрывающие теги с атрибутами (на случай если что-то осталось)
        $html = preg_replace('/<\/(p|br|strong|b|em|i|u|small|ul|ol|li)(\s[^>]*)?>/i', '</$1>', $html);

        return trim($html);
    }
}

