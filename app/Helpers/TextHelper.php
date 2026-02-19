<?php

namespace App\Helpers;

class TextHelper
{
    /**
     * Format mentions (@username) in text with styled links
     * 
     * @param string $content
     * @return string
     */
    public static function formatMentions(string $content): string
    {
        if (empty($content)) {
            return '';
        }
        
        // Escape HTML to prevent XSS
        $escaped = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
        
        // Replace @username mentions with styled links
        // Supports Cyrillic, Latin, numbers, underscores, and hyphens
        $pattern = '/@([a-zA-Zа-яА-ЯёЁіІїЇєЄ0-9_-]+)/u';
        
        return preg_replace_callback($pattern, function ($matches) {
            $username = $matches[1];
            $profileUrl = route('users.public.profile', $username);
            return '<a href="' . e($profileUrl) . '" class="mention-link text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 font-bold transition-colors" target="_blank">@' . e($username) . '</a>';
        }, $escaped);
    }
}

