<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateLibrarySlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'libraries:generate-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Генерує slug для добірок, у яких його немає';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Генерація slug для добірок без slug...');
        
        $libraries = \App\Models\Library::whereNull('slug')->orWhere('slug', '')->get();
        
        if ($libraries->isEmpty()) {
            $this->info('Всі добірки вже мають slug.');
            return 0;
        }
        
        $this->info("Знайдено {$libraries->count()} добірок без slug.");
        
        $bar = $this->output->createProgressBar($libraries->count());
        $bar->start();
        
        foreach ($libraries as $library) {
            $baseSlug = \Illuminate\Support\Str::slug($library->name);
            $slug = $baseSlug;
            $counter = 1;
            
            // Проверяем уникальность slug (исключая текущую запись)
            while (\App\Models\Library::where('slug', $slug)->where('id', '!=', $library->id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            
            $library->slug = $slug;
            $library->save();
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('Генерація slug завершена успішно!');
        
        return 0;
    }
}
