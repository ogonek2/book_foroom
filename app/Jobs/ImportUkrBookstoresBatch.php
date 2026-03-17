<?php

namespace App\Jobs;

use App\Models\UkrBookstoreListing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportUkrBookstoresBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 300;

    public string $query;

    /** @var array<string, array{store_name: string, items: array, error: ?string}> */
    public array $results;

    public function __construct(string $query, array $results)
    {
        $this->query = $query;
        $this->results = $results;
    }

    public function handle(): void
    {
        foreach ($this->results as $storeKey => $data) {
            if (! is_array($data) || ! isset($data['items'])) {
                continue;
            }
            foreach ($data['items'] as $item) {
                if (empty($item['title'] ?? '') || empty($item['url'] ?? '')) {
                    continue;
                }
                UkrBookstoreListing::updateOrCreate(
                    [
                        'store_key' => $storeKey,
                        'url' => $item['url'],
                    ],
                    [
                        'external_id' => $item['external_id'] ?? null,
                        'title' => $item['title'],
                        'price' => $item['price'] ?? null,
                        'image_url' => $item['image_url'] ?? null,
                        'author' => $item['author'] ?? null,
                        'search_query' => $this->query,
                    ]
                );
            }
        }
    }
}
