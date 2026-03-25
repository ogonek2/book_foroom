<?php

namespace App\Console\Commands;

use App\Services\GutenbergService;
use Illuminate\Console\Command;

class FetchGutenberg extends Command
{
    protected $signature = 'books:gutenberg
                            {--q= : Поисковый запрос (например: shakespeare)}
                            {--id= : ID книги (например: 1513)}
                            {--text : Если указан вместе с --id, вернуть текст книги}
                            {--subjects : Получить список subjects}
                            {--book : Если указан вместе с --id, вернуть полную книгу (с авторами)}
                            {--page=1 : Номер страницы для поиска}
                            {--page_size=10 : Размер страницы для поиска}
                            {--cleaning_mode= : cleaning_mode для текста (simple/...) }
                            {--retries= : Сколько раз повторять при 429 (по умолчанию из конфига)}
                            {--sleep_ms= : Пауза между повторами при 429, мс (по умолчанию из конфига)}';

    protected $description = 'Project Gutenberg (RapidAPI): поиск книг и получение текста по ID';

    public function __construct(
        protected GutenbergService $gutenberg,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $id = $this->option('id');
        $q = $this->option('q');
        $this->applyRetryOverrides();

        if ($this->option('subjects')) {
            $result = $this->gutenberg->subjects();
            $this->line(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return ($result['ok'] ?? false) ? self::SUCCESS : self::FAILURE;
        }

        if ($id !== null && $this->option('book')) {
            $bookId = (int) $id;
            if ($bookId <= 0) {
                $this->error('Опция --id должна быть положительным числом.');
                return self::FAILURE;
            }

            $result = $this->gutenberg->getBook($bookId, null);
            $this->line(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return ($result['ok'] ?? false) ? self::SUCCESS : self::FAILURE;
        }

        if ($id !== null && $this->option('text')) {
            $bookId = (int) $id;
            if ($bookId <= 0) {
                $this->error('Опция --id должна быть положительным числом.');
                return self::FAILURE;
            }

            $mode = $this->option('cleaning_mode') ?: null;
            $result = $this->gutenberg->getText($bookId, is_string($mode) ? $mode : null);

            $this->line(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return ($result['ok'] ?? false) ? self::SUCCESS : self::FAILURE;
        }

        if (! is_string($q) || trim($q) === '') {
            $this->error('Нужно указать либо --q="<query>" для поиска, либо --id=<id> --text для текста.');
            return self::FAILURE;
        }

        $page = max(1, (int) $this->option('page'));
        $pageSize = max(1, (int) $this->option('page_size'));

        $result = $this->gutenberg->search($q, $pageSize, $page);
        $this->line(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        return ($result['ok'] ?? false) ? self::SUCCESS : self::FAILURE;
    }

    protected function applyRetryOverrides(): void
    {
        $retries = $this->option('retries');
        $sleepMs = $this->option('sleep_ms');

        if ($retries !== null && $retries !== '') {
            config(['gutenberg.retry_times' => max(0, (int) $retries)]);
        }
        if ($sleepMs !== null && $sleepMs !== '') {
            config(['gutenberg.retry_sleep_ms' => max(0, (int) $sleepMs)]);
        }
    }
}

