<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ToolsController extends Controller
{
    public function index()
    {
        return view('admin.tools.index');
    }

    public function siteHealth()
    {
        $health = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'CLI',
            'max_upload_size' => ini_get('upload_max_filesize'),
            'max_post_size' => ini_get('post_max_size'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time') . 's',
            'db_driver' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'disk_free' => round(disk_free_space(base_path()) / 1073741824, 2) . ' GB',
            'disk_total' => round(disk_total_space(base_path()) / 1073741824, 2) . ' GB',
        ];

        // Database size
        $dbSize = '—';
        try {
            $driver = config('database.default');
            if ($driver === 'mysql') {
                $result = DB::select("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb FROM information_schema.tables WHERE table_schema = ?", [config('database.connections.mysql.database')]);
                $dbSize = ($result[0]->size_mb ?? 0) . ' MB';
            } elseif ($driver === 'sqlite') {
                $path = config('database.connections.sqlite.database');
                $dbSize = file_exists($path) ? round(filesize($path) / 1024, 2) . ' KB' : '—';
            }
        } catch (\Exception $e) {
            $dbSize = 'Unable to determine';
        }
        $health['db_size'] = $dbSize;

        // PHP Extensions
        $health['extensions'] = [
            'openssl' => extension_loaded('openssl'),
            'pdo' => extension_loaded('pdo'),
            'mbstring' => extension_loaded('mbstring'),
            'tokenizer' => extension_loaded('tokenizer'),
            'xml' => extension_loaded('xml'),
            'ctype' => extension_loaded('ctype'),
            'json' => extension_loaded('json'),
            'bcmath' => extension_loaded('bcmath'),
            'fileinfo' => extension_loaded('fileinfo'),
            'gd' => extension_loaded('gd'),
            'curl' => extension_loaded('curl'),
        ];

        return view('admin.tools.site-health', compact('health'));
    }

    public function importExport()
    {
        return view('admin.tools.import-export');
    }

    public function doExport(Request $request)
    {
        $posts = Post::with(['category', 'author', 'tags'])->get();

        $export = $posts->map(function ($post) {
            return [
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'content' => $post->content,
                'status' => $post->status,
                'category' => $post->category?->name,
                'author' => $post->author?->name,
                'tags' => $post->tags->pluck('name')->toArray(),
                'featured_image' => $post->featured_image,
                'is_featured' => $post->is_featured,
                'views_count' => $post->views_count,
                'reading_time' => $post->reading_time,
                'published_at' => $post->published_at?->toISOString(),
                'created_at' => $post->created_at->toISOString(),
            ];
        });

        $filename = 'atomni-export-' . now()->format('Y-m-d-His') . '.json';

        return response()->json($export)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->header('Content-Type', 'application/json');
    }

    public function doImport(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:json,txt|max:10240']);

        $content = file_get_contents($request->file('file')->getRealPath());
        $data = json_decode($content, true);

        if (!is_array($data)) {
            return back()->with('error', 'Invalid JSON file.');
        }

        $imported = 0;
        foreach ($data as $item) {
            if (empty($item['title'])) continue;

            $category = null;
            if (!empty($item['category'])) {
                $category = Category::firstOrCreate(
                    ['slug' => Str::slug($item['category'])],
                    ['name' => $item['category']]
                );
            }

            $post = Post::updateOrCreate(
                ['slug' => $item['slug'] ?? Str::slug($item['title'])],
                [
                    'title' => $item['title'],
                    'excerpt' => $item['excerpt'] ?? null,
                    'content' => $item['content'] ?? '',
                    'status' => $item['status'] ?? 'draft',
                    'category_id' => $category?->id,
                    'author_id' => auth()->id(),
                    'featured_image' => $item['featured_image'] ?? null,
                    'is_featured' => $item['is_featured'] ?? false,
                    'reading_time' => $item['reading_time'] ?? null,
                    'published_at' => $item['published_at'] ?? null,
                ]
            );
            $imported++;
        }

        return back()->with('success', "Successfully imported {$imported} posts.");
    }

    public function cacheManager()
    {
        return view('admin.tools.cache-manager');
    }

    public function clearCache(Request $request)
    {
        $type = $request->input('type', 'all');

        $messages = [];
        if ($type === 'views' || $type === 'all') {
            Artisan::call('view:clear');
            $messages[] = 'View cache cleared';
        }
        if ($type === 'cache' || $type === 'all') {
            Artisan::call('cache:clear');
            $messages[] = 'Application cache cleared';
        }
        if ($type === 'config' || $type === 'all') {
            Artisan::call('config:clear');
            $messages[] = 'Config cache cleared';
        }
        if ($type === 'routes' || $type === 'all') {
            Artisan::call('route:clear');
            $messages[] = 'Route cache cleared';
        }

        return back()->with('success', implode('. ', $messages) . '.');
    }

    /**
     * Trigger the RSS import command via AJAX from the admin Settings → RSS page.
     */
    public function runRssImport(Request $request)
    {
        $urlsRaw = \App\Models\Setting::get('rss_custom_urls', '');
        $urls    = array_filter(array_map('trim', explode("\n", $urlsRaw)));

        if (empty($urls)) {
            return response()->json([
                'success' => false,
                'message' => 'No RSS feed URLs configured. Add at least one URL in the External RSS Feeds box above.',
            ], 422);
        }

        try {
            // Prevent PHP timeout when fetching many feeds
            set_time_limit(0);

            $exitCode = Artisan::call('rss:import');
            $output   = Artisan::output();

            // Parse import count from command output
            preg_match('/(\d+) posts imported/', $output, $matches);
            $imported = (int) ($matches[1] ?? 0);

            return response()->json([
                'success'      => true,
                'message'      => "Done! {$imported} post(s) published successfully.",
                'imported'     => $imported,
                'last_imported'=> now()->format('d M Y, H:i'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
