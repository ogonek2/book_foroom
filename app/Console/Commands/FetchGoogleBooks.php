<?php

namespace App\Console\Commands;

use App\Services\GoogleBooksService;
use Illuminate\Console\Command;

class FetchGoogleBooks extends Command
{
    protected $signature = 'books:fetch-google 
                            {query? : Поисковый запрос для Google Books (по умолчанию из конфигурации)} 
                            {--max=10000 : Максимальное количество книг, которое нужно попытаться получить}';

    protected $description = 'Спарсить Google Books API и вывести, сколько книг (без русских авторов) удалось получить';

    public function __construct(
        protected GoogleBooksService $googleBooks,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $query = $this->argument('query') ?? config('googlebooks.default_query', 'subject:books');

        $maxOption = (int) $this->option('max');
        $maxTotal = $maxOption > 0 ? $maxOption : 10000;

        $this->info("Старт парсинга Google Books API");
        $this->line("Запрос: {$query}");
        $this->line("Максимум книг для попытки получения: {$maxTotal}");

        $count = $this->googleBooks->countAllExcludingRussian($query, $maxTotal);

        $this->newLine();
        $this->info("Итого получено книг (без русских авторов и русскоязычных книг): {$count}");

        return self::SUCCESS;
    }
}

