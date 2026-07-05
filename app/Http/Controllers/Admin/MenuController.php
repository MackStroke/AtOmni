<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * List all menus.
     */
    public function index()
    {
        $menus = Menu::withCount('items')->orderBy('name')->get();
        return view('admin.menus.index', compact('menus'));
    }

    /**
     * Edit a menu — manage its items.
     */
    public function edit(Menu $menu)
    {
        $menu->load(['items' => function ($q) {
            $q->whereNull('parent_id')->orderBy('order')->with(['children' => function ($q) {
                $q->orderBy('order');
            }]);
        }]);

        return view('admin.menus.edit', compact('menu'));
    }

    /**
     * Update a menu's properties and all its items.
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $menu->update([
            'name'      => $request->input('name'),
            'is_active' => $request->boolean('is_active'),
        ]);

        // Rebuild items from the submitted JSON
        $itemsJson = $request->input('items_json', '[]');
        $items = json_decode($itemsJson, true);

        if (is_array($items)) {
            // Delete all existing items and rebuild
            $menu->items()->delete();

            foreach ($items as $order => $item) {
                $parent = MenuItem::create([
                    'menu_id'   => $menu->id,
                    'title'     => $item['title'] ?? 'Untitled',
                    'url'       => $item['url'] ?? '#',
                    'target'    => $item['target'] ?? '_self',
                    'icon'      => $item['icon'] ?? null,
                    'css_class' => $item['css_class'] ?? null,
                    'order'     => $order,
                    'is_active' => $item['is_active'] ?? true,
                    'parent_id' => null,
                ]);

                // If this item has children
                if (!empty($item['children']) && is_array($item['children'])) {
                    foreach ($item['children'] as $cOrder => $child) {
                        MenuItem::create([
                            'menu_id'   => $menu->id,
                            'parent_id' => $parent->id,
                            'title'     => $child['title'] ?? 'Untitled',
                            'url'       => $child['url'] ?? '#',
                            'target'    => $child['target'] ?? '_self',
                            'icon'      => $child['icon'] ?? null,
                            'css_class' => $child['css_class'] ?? null,
                            'order'     => $cOrder,
                            'is_active' => $child['is_active'] ?? true,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.menus.edit', $menu)->with('success', 'Menu updated successfully.');
    }

    /**
     * Create a new menu.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'location' => 'required|string|max:255|unique:menus,location',
        ]);

        $menu = Menu::create([
            'name'      => $request->input('name'),
            'location'  => $request->input('location'),
            'is_active' => true,
        ]);

        return redirect()->route('admin.menus.edit', $menu)->with('success', 'Menu created.');
    }

    /**
     * Delete a menu.
     */
    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('admin.menus.index')->with('success', 'Menu deleted.');
    }
}
