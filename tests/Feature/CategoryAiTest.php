<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Admin\CategoryController;

class CategoryAiTest extends TestCase
{
    use RefreshDatabase;

    public function test_auto_fill_category_success()
    {
        // 1. Mock Setting and Category
        Setting::updateOrCreate(['key' => 'gemini_key'], ['value' => 'mock-gemini-key']);
        
        $category = Category::create([
            'name' => 'Sports Gear',
            'slug' => 'sports-gear',
            'description' => '',
            'color_code' => '#123456',
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
                                        'description' => 'High quality gear for all outdoor and indoor sports.',
                                        'color_code' => '#FF5733'
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        // 3. Instantiate CategoryController and run method
        $controller = new CategoryController();
        $result = $controller->autoFillCategory($category);

        // 4. Assert response and database state
        $this->assertTrue($result['success']);
        $this->assertEquals('High quality gear for all outdoor and indoor sports.', $result['description']);
        $this->assertEquals('#FF5733', $result['color_code']);

        $category->refresh();
        $this->assertEquals('High quality gear for all outdoor and indoor sports.', $category->description);
        $this->assertEquals('#FF5733', $category->color_code);
    }

    public function test_auto_fill_category_fails_without_key()
    {
        // No key set in Settings
        $category = Category::create([
            'name' => 'Sports Gear',
            'slug' => 'sports-gear',
            'description' => '',
            'color_code' => '#123456',
        ]);

        $controller = new CategoryController();
        $result = $controller->autoFillCategory($category);

        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('No AI API key configured', $result['error']);
    }

    public function test_auto_fill_category_fallback_to_anthropic()
    {
        // Gemini fails (429), Anthropic succeeds
        Setting::updateOrCreate(['key' => 'gemini_key'], ['value' => 'mock-gemini-key']);
        Setting::updateOrCreate(['key' => 'anthropic_key'], ['value' => 'mock-anthropic-key']);

        $category = Category::create([
            'name' => 'Sports Gear',
            'slug' => 'sports-gear',
            'description' => '',
            'color_code' => '#123456',
        ]);

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response(['error' => ['message' => 'Quota exceeded']], 429),
            'https://api.anthropic.com/*' => Http::response([
                'content' => [
                    [
                        'text' => json_encode([
                            'description' => 'Anthropic description.',
                            'color_code' => '#AABBCC'
                        ])
                    ]
                ]
            ], 200)
        ]);

        $controller = new CategoryController();
        $result = $controller->autoFillCategory($category);

        $this->assertTrue($result['success']);
        $this->assertEquals('Anthropic description.', $result['description']);
        $this->assertEquals('#AABBCC', $result['color_code']);
    }

    public function test_auto_fill_category_fallback_to_openai()
    {
        // Gemini and Anthropic fail, OpenAI succeeds
        Setting::updateOrCreate(['key' => 'gemini_key'], ['value' => 'mock-gemini-key']);
        Setting::updateOrCreate(['key' => 'anthropic_key'], ['value' => 'mock-anthropic-key']);
        Setting::updateOrCreate(['key' => 'open_ai_key'], ['value' => 'mock-openai-key']);

        $category = Category::create([
            'name' => 'Sports Gear',
            'slug' => 'sports-gear',
            'description' => '',
            'color_code' => '#123456',
        ]);

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response(['error' => ['message' => 'Quota exceeded']], 429),
            'https://api.anthropic.com/*' => Http::response(['error' => ['message' => 'Overloaded']], 503),
            'https://api.openai.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'description' => 'OpenAI description.',
                                'color_code' => '#FFAA00'
                            ])
                        ]
                    ]
                ]
            ], 200)
        ]);

        $controller = new CategoryController();
        $result = $controller->autoFillCategory($category);

        $this->assertTrue($result['success']);
        $this->assertEquals('OpenAI description.', $result['description']);
        $this->assertEquals('#FFAA00', $result['color_code']);
    }
}
