<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Media;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Admin\AiController;

class MediaAiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user since user_id is a required field on the media table
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_auto_fill_media_success_openai()
    {
        // 1. Mock OpenAI Setting and Media
        Setting::updateOrCreate(['key' => 'open_ai_key'], ['value' => 'mock-api-key']);
        
        Storage::fake('public');
        Storage::disk('public')->put('media/test-image.jpg', 'fake-image-content');

        $media = Media::create([
            'user_id' => $this->user->id,
            'file_name' => 'test-image.jpg',
            'file_path' => 'media/test-image.jpg',
            'mime_type' => 'image/jpeg',
            'size_kb' => 10,
        ]);

        // 2. Mock Http response from OpenAI
        Http::fake([
            'https://api.openai.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'alt_text' => 'A beautiful sunset over the mountains',
                                'file_name' => 'beautiful-sunset-mountains'
                            ])
                        ]
                    ]
                ]
            ], 200)
        ]);

        // 3. Instantiate AiController and run method
        $controller = new AiController();
        $result = $controller->autoFillMedia($media);

        // 4. Assert response and database state
        $this->assertTrue($result['success']);
        $this->assertEquals('A beautiful sunset over the mountains', $result['alt_text']);
        $this->assertEquals('beautiful-sunset-mountains.jpg', $result['file_name']);

        $media->refresh();
        $this->assertEquals('A beautiful sunset over the mountains', $media->alt_text);
        $this->assertEquals('beautiful-sunset-mountains.jpg', $media->file_name);
    }

    public function test_auto_fill_media_success_gemini()
    {
        // 1. Mock Gemini Setting and Media
        Setting::updateOrCreate(['key' => 'gemini_key'], ['value' => 'mock-gemini-key']);
        
        Storage::fake('public');
        Storage::disk('public')->put('media/test-image.jpg', 'fake-image-content');

        $media = Media::create([
            'user_id' => $this->user->id,
            'file_name' => 'test-image.jpg',
            'file_path' => 'media/test-image.jpg',
            'mime_type' => 'image/jpeg',
            'size_kb' => 10,
        ]);

        // 2. Mock Http response from Gemini
        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'alt_text' => 'Gemini sunset alt',
                                        'file_name' => 'gemini-sunset-file'
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        // 3. Instantiate AiController and run method
        $controller = new AiController();
        $result = $controller->autoFillMedia($media);

        // 4. Assert response and database state
        $this->assertTrue($result['success']);
        $this->assertEquals('Gemini sunset alt', $result['alt_text']);
        $this->assertEquals('gemini-sunset-file.jpg', $result['file_name']);
    }

    public function test_auto_fill_media_success_anthropic()
    {
        // 1. Mock Anthropic Setting and Media
        Setting::updateOrCreate(['key' => 'anthropic_key'], ['value' => 'mock-anthropic-key']);
        
        Storage::fake('public');
        Storage::disk('public')->put('media/test-image.jpg', 'fake-image-content');

        $media = Media::create([
            'user_id' => $this->user->id,
            'file_name' => 'test-image.jpg',
            'file_path' => 'media/test-image.jpg',
            'mime_type' => 'image/jpeg',
            'size_kb' => 10,
        ]);

        // 2. Mock Http response from Anthropic
        Http::fake([
            'https://api.anthropic.com/*' => Http::response([
                'content' => [
                    [
                        'text' => json_encode([
                            'alt_text' => 'Anthropic sunset alt',
                            'file_name' => 'anthropic-sunset-file'
                        ])
                    ]
                ]
            ], 200)
        ]);

        // 3. Instantiate AiController and run method
        $controller = new AiController();
        $result = $controller->autoFillMedia($media);

        // 4. Assert response and database state
        $this->assertTrue($result['success']);
        $this->assertEquals('Anthropic sunset alt', $result['alt_text']);
        $this->assertEquals('anthropic-sunset-file.jpg', $result['file_name']);
    }

    public function test_auto_fill_media_fails_without_key()
    {
        // No key set in Settings
        Storage::fake('public');
        $media = Media::create([
            'user_id' => $this->user->id,
            'file_name' => 'test-image.jpg',
            'file_path' => 'media/test-image.jpg',
            'mime_type' => 'image/jpeg',
            'size_kb' => 10,
        ]);

        $controller = new AiController();
        $result = $controller->autoFillMedia($media);

        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('No AI API key configured', $result['error']);
    }
}
