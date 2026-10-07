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
        $name = trim((string) $request->input('name'));

        if (empty($name)) {
            return redirect()->back()->withInput()->with('error', 'Le nom de la catégorie est obligatoire.');
        }

        // Vérification d'existence préalable pour éviter les doublons (insensible à la casse)
        $exists = Category::whereRaw('LOWER(name) = ?', [strtolower($name)])->exists();

        if ($exists) {
            return redirect()->back()->withInput()->with('error', "La création a été annulée : une catégorie nommée « {$name} » existe déjà.");
        }

        $slug = Str::slug($name);

        Category::create([
            'name' => $name,
            'slug' => $slug,
        ]);

        return redirect()->route('admin.categories.index')->with('success', "La catégorie « {$name} » a été créée avec succès.");
    }

    /**
     * Update a category.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $name = trim((string) $request->input('name'));

        if (empty($name)) {
            return redirect()->back()->with('error', 'Le nom de la catégorie ne peut pas être vide.');
        }

        $exists = Category::whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->where('id', '!=', $category->id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', "Modification annulée : une autre catégorie nommée « {$name} » existe déjà.");
        }

        $category->update([
            'name' => $name,
            'slug' => Str::slug($name),
        ]);

        return redirect()->route('admin.categories.index')->with('success', "La catégorie a été mise à jour vers « {$name} ».");
    }

    /**
     * Delete a category.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $booksCount = $category->books()->count();

        if ($booksCount > 0) {
            return redirect()->route('admin.categories.index')->with('error', "Impossible de supprimer la catégorie « {$category->name} » car elle contient {$booksCount} livre(s). Veuillez d'abord réattribuer ces livres pour éviter les ouvrages orphelins.");
        }

        $categoryName = $category->name;
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', "La catégorie « {$categoryName} » a été supprimée avec succès.");
    }
}
