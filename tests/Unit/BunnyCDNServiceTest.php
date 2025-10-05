<?php

namespace Tests\Unit;

use App\Services\BunnyCDNService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BunnyCDNServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test URL generation
     */
    public function test_get_url_generation(): void
    {
        $service = app(BunnyCDNService::class);
        
        $path = 'avatars/test/example.jpg';
        $url = $service->getUrl($path);
        
        $this->assertStringContainsString('avatars/test/example.jpg', $url);
        $this->assertStringStartsWith('http', $url);
    }

    /**
     * Test path generation
     */
    public function test_path_generation(): void
    {
        $service = app(BunnyCDNService::class);
        
        // Create a mock uploaded file
        $mockFile = $this->createMock(\Illuminate\Http\UploadedFile::class);
        $mockFile->method('getClientOriginalExtension')->willReturn('jpg');
        
        // Use reflection to test protected method
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('generatePath');
        $method->setAccessible(true);
        
        $path = $method->invoke($service, $mockFile);
        
        $this->assertStringStartsWith('avatars/', $path);
        $this->assertStringEndsWith('.jpg', $path);
    }

    /**
     * Test BunnyCDN path detection
     */
    public function test_bunnycdn_path_detection(): void
    {
        $service = app(BunnyCDNService::class);
        
        // Test BunnyCDN path (doesn't start with 'avatars/')
        $bunnyPath = 'some/path/to/file.jpg';
        $this->assertTrue($service->isBunnyCDNPath($bunnyPath));
        
        // Test local path (starts with 'avatars/')
        $localPath = 'avatars/user123.jpg';
        $this->assertFalse($service->isBunnyCDNPath($localPath));
    }
}
