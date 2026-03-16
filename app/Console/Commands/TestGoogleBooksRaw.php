<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestGoogleBooksRaw extends Command
{
    protected $signature = 'books:test-google-raw
                            {query? : Поисковый запрос для Google Books}
                            {--max=10 : Сколько элементов показать}';

    protected $description = 'Показать «сырой» ответ Google Books API (totalItems и несколько первых items) без фильтрации';

    public function handle(): int
    {
        $query = $this->argument('query') ?? config('googlebooks.default_query', 'subject:fiction+language:en');
        $limit = (int) $this->option('max');
        $limit = $limit > 0 ? $limit : 10;

        $this->info("Запрос к Google Books: {$query}");

        $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
            'q' => $query,
            'maxResults' => $limit,
            'startIndex' => 0,
            'printType' => 'books',
            'projection' => 'lite',
            'key' => config('googlebooks.api_key'),
        ]);

        if (! $response->successful()) {
            $this->error('Запрос неуспешен.');
            $this->line('HTTP статус: ' . $response->status());
            $this->line('Тело ответа: ' . $response->body());

            return self::FAILURE;
        }

        $data = $response->json();

        $this->line('totalItems: ' . ($data['totalItems'] ?? 'нет'));
        $items = $data['items'] ?? [];
        $this->line('items count: ' . count($items));

        foreach (array_slice($items, 0, $limit) as $index => $item) {
            $volumeInfo = $item['volumeInfo'] ?? [];
            $this->newLine();
            $this->info("Item #{$index}");
            $this->line('  id: ' . ($item['id'] ?? 'нет'));
            $this->line('  title: ' . ($volumeInfo['title'] ?? 'нет'));
            $this->line('  language: ' . ($volumeInfo['language'] ?? 'нет'));
            $this->line('  authors: ' . (isset($volumeInfo['authors']) ? implode(', ', $volumeInfo['authors']) : 'нет'));
        }

        return self::SUCCESS;
    }
}

