<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BulkActionController extends Controller
{
    /**
     * Map resource slugs to Eloquent models.
     */
    protected array $modelMap = [
        'posts' => \App\Models\Post::class,
        'categories' => \App\Models\Category::class,
        'users' => \App\Models\User::class,
        'pages' => \App\Models\Page::class,
        'team-members' => \App\Models\TeamMember::class,
        'comments' => \App\Models\Comment::class,
        'contacts' => \App\Models\ContactQuery::class,
        'donors' => \App\Models\Donor::class,
        'jobs' => \App\Models\JobPosting::class,
        'applications' => \App\Models\JobApplication::class,
        'newsletter' => \App\Models\NewsletterSubscriber::class,
        'media' => \App\Models\Media::class,
    ];

    /**
     * Handle generic bulk actions for any mapped resource.
     */
    public function handle(Request $request, $resource, \App\Services\TaxonomyService $taxonomyService)
    {
        // 1. Validate the resource
        if (!array_key_exists($resource, $this->modelMap)) {
            return back()->with('error', "Bulk actions not supported for resource '{$resource}'.");
        }

        $modelClass = $this->modelMap[$resource];

        // 2. Validate input
        $validated = $request->validate([
            'action' => 'required|string',
            'ids' => 'required|array|min:1',
            'ids.*' => 'numeric',
        ], [
            'ids.required' => 'Please select at least one item to perform this action.',
            'ids.min' => 'Please select at least one item.',
        ]);

        $action = $validated['action'];
        $ids = $validated['ids'];

        // 3. Prevent self-deletion for Users
        if ($resource === 'users' && $action === 'delete') {
            $ids = array_filter($ids, fn($id) => $id != auth()->id());
            if (empty($ids)) {
                return back()->with('error', 'You cannot delete yourself.');
            }
        }

        // 4. Perform Action
        DB::beginTransaction();
        try {
            $count = count($ids);
            
            switch ($action) {
                case 'delete':
                    // Check if model uses soft deletes or if we should manually iterate to trigger deleting events.
                    // Instead of bulk delete query, iterate so Model::delete() events (like deleting files) fire.
                    $items = $modelClass::whereIn('id', $ids)->get();
                    foreach ($items as $item) {
                        $item->delete();
                    }
                    $message = "Successfully deleted {$count} item(s).";
                    break;
                    
                case 'draft':
                    $modelClass::whereIn('id', $ids)->update(['status' => 'draft']);
                    $message = "Successfully updated {$count} item(s) to draft.";
                    break;
                    
                case 'publish':
                    $modelClass::whereIn('id', $ids)->update([
                        'status' => 'published',
                        'published_at' => \Carbon\Carbon::now()
                    ]);
                    $message = "Successfully published {$count} item(s).";
                    break;
                    
                case 'analyze_taxonomy':
                case 'auto_taxonomy':
                    if ($resource !== 'posts') {
                        throw new \Exception("Taxonomy analysis is only available for posts.");
                    }
                    \App\Jobs\AnalyzePostsJob::dispatch('taxonomy', $ids);
                    $message = "Taxonomy analysis queued for {$count} post(s). It will process in the background.";
                    break;
                    
                case 'analyze_scores':
                    if ($resource !== 'posts') {
                        throw new \Exception("Score analysis is only available for posts.");
                    }
                    \App\Jobs\AnalyzePostsJob::dispatch('scores', $ids);
                    $message = "Score analysis queued for {$count} post(s). It will process in the background.";
                    break;
                    
                case 'approve': // For comments
                    $modelClass::whereIn('id', $ids)->update(['is_approved' => true]);
                    $message = "Successfully approved {$count} comment(s).";
                    break;

                case 'unapprove': // For comments
                    $modelClass::whereIn('id', $ids)->update(['is_approved' => false]);
                    $message = "Successfully unapproved {$count} comment(s).";
                    break;

                case 'auto_fill':
                    if ($resource === 'media') {
                        // Commit active transaction so database connection isn't held open during external API requests
                        DB::commit();
                        
                        $items = $modelClass::whereIn('id', $ids)->get();
                        $processed = 0;
                        
                        $aiController = app(\App\Http\Controllers\Admin\AiController::class);
                        
                        foreach ($items as $item) {
                            if (str_starts_with($item->mime_type, 'image/')) {
                                $res = $aiController->autoFillMedia($item);
                                if (isset($res['success']) && $res['success']) {
                                    $processed++;
                                }
                            }
                        }
                        $message = "Successfully auto-filled SEO metadata for {$processed} image(s).";
                        // Re-start transaction since the wrapper expects transaction block to be active at the end to commit
                        DB::beginTransaction();
                    } elseif ($resource === 'categories') {
                        // Commit active transaction so database connection isn't held open during external API requests
                        DB::commit();
                        
                        $items = $modelClass::whereIn('id', $ids)->get();
                        $processed = 0;
                        
                        $categoryController = app(\App\Http\Controllers\Admin\CategoryController::class);
                        
                        foreach ($items as $item) {
                            $res = $categoryController->autoFillCategory($item);
                            if (isset($res['success']) && $res['success']) {
                                $processed++;
                            }
                        }
                        $message = "Successfully auto-filled SEO metadata for {$processed} category(s).";
                        // Re-start transaction since the wrapper expects transaction block to be active at the end to commit
                        DB::beginTransaction();
                    } else {
                        throw new \Exception("Auto-fill is not supported for resource '{$resource}'.");
                    }
                    break;

                default:
                    DB::rollBack();
                    return back()->with('error', 'Unknown bulk action.');
            }

            DB::commit();
            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Bulk Action Error ({$resource} -> {$action}): " . $e->getMessage());
            return back()->with('error', 'An error occurred while processing the bulk action.');
        }
    }
}
