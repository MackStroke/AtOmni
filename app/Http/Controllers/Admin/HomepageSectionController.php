<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

class HomepageSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections = HomepageSection::orderBy('order')->get();
        return view('admin.homepage-sections.index', compact('sections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        return view('admin.homepage-sections.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'layout_type' => 'required|string|in:3d_carousel,tech_complex_grid,horizontal_scroll,standard_grid',
            'category_id' => 'nullable|exists:categories,id',
            'tag_id' => 'nullable|exists:tags,id',
            'post_limit' => 'required|integer|min:1|max:20',
            'order' => 'required|integer',
            'is_active' => 'boolean',
                        'filters' => 'nullable|array',
            'filters.tag_ids' => 'nullable|array',
            'filters.tag_ids.*' => 'exists:tags,id',
        ]);

        $validated['is_active'] = $request->has('is_active');

        HomepageSection::create($validated);

        return redirect()->route('admin.homepage-sections.index')
                         ->with('success', 'Homepage section created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HomepageSection $homepageSection)
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        return view('admin.homepage-sections.edit', compact('homepageSection', 'categories', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HomepageSection $homepageSection)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'layout_type' => 'required|string|in:3d_carousel,tech_complex_grid,horizontal_scroll,standard_grid',
            'category_id' => 'nullable|exists:categories,id',
            'tag_id' => 'nullable|exists:tags,id',
            'post_limit' => 'required|integer|min:1|max:20',
            'order' => 'required|integer',
            'is_active' => 'boolean',
                        'filters' => 'nullable|array',
            'filters.tag_ids' => 'nullable|array',
            'filters.tag_ids.*' => 'exists:tags,id',
        ]);

        $validated['is_active'] = $request->has('is_active');
        // Ensure filters is properly handled if null or empty array
        $validated['filters'] = $request->input('filters', []);

        $homepageSection->update($validated);

        return redirect()->route('admin.homepage-sections.index')
                         ->with('success', 'Homepage section updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HomepageSection $homepageSection)
    {
        $homepageSection->delete();

        return redirect()->route('admin.homepage-sections.index')
                         ->with('success', 'Homepage section deleted successfully.');
    }

    /**
     * Update the order of the sections.
     */
    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|exists:homepage_sections,id',
        ]);

        foreach ($validated['order'] as $index => $id) {
            HomepageSection::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
