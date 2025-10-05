<?php

namespace App\Console\Commands;

use App\Helpers\CDNUploader;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TestCDNUpload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cdn:test-upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test CDN upload functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing CDN upload...');

        // Check configuration
        $this->info('📋 Checking configuration...');
        
        $config = [
            'Storage Name' => config('bunnycdn.storage.name') ? '✅ Set' : '❌ Missing',
            'Storage Password' => config('bunnycdn.storage.password') ? '✅ Set' : '❌ Missing',
            'CDN URL' => config('bunnycdn.cdn.url') ? '✅ Set' : '❌ Missing',
        ];

        foreach ($config as $key => $status) {
            $this->line("  {$status} {$key}");
        }

        // Check if all required config is present
        $storageName = config('bunnycdn.storage.name');
        $storagePassword = config('bunnycdn.storage.password');
        $cdnUrl = config('bunnycdn.cdn.url');
        
        if (!$storageName || !$storagePassword || !$cdnUrl) {
            $this->error('❌ Missing required configuration. Please check your .env file.');
            $this->line('Required variables: BUNNY_STORAGE_NAME, BUNNY_STORAGE_PASSWORD, BUNNY_CDN_URL');
            $this->line('Current values:');
            $this->line('  Storage Name: ' . ($storageName ?: 'Missing'));
            $this->line('  Storage Password: ' . ($storagePassword ? 'Set' : 'Missing'));
            $this->line('  CDN URL: ' . ($cdnUrl ?: 'Missing'));
            return 1;
        }

        $this->info('✅ Configuration looks good!');

        // Create a test file
        $this->info('📁 Creating test file...');
        $testContent = 'This is a test file for CDN upload.';
        $testPath = storage_path('app/test.txt');
        file_put_contents($testPath, $testContent);

        try {
            // Create UploadedFile instance
            $uploadedFile = new UploadedFile(
                $testPath,
                'test.txt',
                'text/plain',
                null,
                true
            );

            $this->info('🚀 Uploading to CDN...');
            
            // Upload to CDN
            $url = CDNUploader::uploadFile($uploadedFile, 'test');
            
            $this->info('✅ Upload successful!');
            $this->line("📎 File URL: {$url}");
            
            // Clean up test file
            unlink($testPath);
            
            $this->info('🎉 CDN upload test completed successfully!');
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Upload failed: ' . $e->getMessage());
            
            // Clean up test file
            if (file_exists($testPath)) {
                unlink($testPath);
            }
            
            return 1;
        }
    }
}
