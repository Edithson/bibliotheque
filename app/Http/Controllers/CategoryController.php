<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display category list for Gerants and Admins.
     */
    public function index(): View
    {
        $categories = Category::withCount('books')->orderBy('name')->paginate(15);

        return view('admin.pages.categories.index', compact('categories'));
    }

    /**
     * Store a new category.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
        ]);

        $slug = Str::slug($validated['name']);
        Category::create([
            'name' => $validated['name'],
            'slug' => $slug,
        ]);

        return redirect()->route('admin.categories.index')->with('success', "La catégorie « {$validated['name']} » a été créée.");
    }

    /**
     * Update a category.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,'.$category->id],
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'La catégorie a été mise à jour.');
    }

    /**
     * Delete a category.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'La catégorie a été supprimée.');
    }
}
