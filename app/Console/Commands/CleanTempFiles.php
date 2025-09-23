<?php

namespace App\Console\Commands;

use App\Helpers\FileHelper;
use Illuminate\Console\Command;

class CleanTempFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-temp-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Очистить временные файлы импорта';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Очистка временных файлов...');
        
        FileHelper::cleanTempFiles();
        
        $this->info('Временные файлы очищены!');
        
        return 0;
    }
}
