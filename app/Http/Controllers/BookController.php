<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookController extends Controller
{
    /**
     * Display public boutique interface.
     */
    public function shopIndex(): View
    {
        $books = Book::with('category')->where('is_published', true)->get();
        $categories = Category::all();

        $formattedBooks = $books->map(fn (Book $b) => [
            'id' => $b->id,
            't' => $b->title,
            'slug' => $b->slug,
            'a' => $b->author ?? 'Auteur inconnu',
            'g' => $b->category ? $b->category->name : 'Général',
            'category_id' => $b->category_id,
            'c' => $b->cover_color ?? '#5b1a1f',
            'w' => $b->cover_width ?? 50,
            'h' => $b->cover_height ?? 200,
            'p' => $b->price,
            's' => $b->stock,
            'd' => $b->description ?? '',
            'x' => $b->excerpt ?? ($b->description ?? ''),
        ])->values()->all();

        return view('shop.index', [
            'books' => $formattedBooks,
            'categories' => $categories,
        ]);
    }

    /**
     * Display administration ledger interface.
     */
    public function adminIndex(): View
    {
        $books = Book::with('category')->orderBy('id', 'desc')->get();
        $categories = Category::all();

        $formattedBooks = $books->map(fn (Book $b) => [
            'id' => $b->id,
            't' => $b->title,
            'slug' => $b->slug,
            'a' => $b->author ?? 'Inconnu',
            'g' => $b->category ? $b->category->name : 'Général',
            'category_id' => $b->category_id,
            'p' => $b->price,
            's' => $b->stock,
            'c' => $b->cover_color ?? '#5b1a1f',
            'd' => $b->description ?? '',
            'x' => $b->excerpt ?? '',
        ])->values()->all();

        return view('admin.index', [
            'books' => $formattedBooks,
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created book in storage.
     */
    public function store(StoreBookRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();
        $validated['slug'] = $this->generateUniqueSlug($validated['title']);

        $book = Book::create($validated);
        $book->load('category');

        $formattedBook = [
            'id' => $book->id,
            't' => $book->title,
            'slug' => $book->slug,
            'a' => $book->author ?? 'Inconnu',
            'g' => $book->category ? $book->category->name : 'Général',
            'category_id' => $book->category_id,
            'p' => $book->price,
            's' => $book->stock,
            'c' => $book->cover_color ?? '#5b1a1f',
            'd' => $book->description ?? '',
            'x' => $book->excerpt ?? '',
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ouvrage ajouté au catalogue avec succès.',
                'book' => $formattedBook,
            ], 201);
        }

        return redirect()->route('admin.index')->with('success', 'Ouvrage ajouté au catalogue avec succès.');
    }

    /**
     * Update the specified book in storage.
     */
    public function update(UpdateBookRequest $request, Book $book): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        if ($book->title !== $validated['title']) {
            $validated['slug'] = $this->generateUniqueSlug($validated['title'], $book->id);
        }

        $book->update($validated);
        $book->load('category');

        $formattedBook = [
            'id' => $book->id,
            't' => $book->title,
            'slug' => $book->slug,
            'a' => $book->author ?? 'Inconnu',
            'g' => $book->category ? $book->category->name : 'Général',
            'category_id' => $book->category_id,
            'p' => $book->price,
            's' => $book->stock,
            'c' => $book->cover_color ?? '#5b1a1f',
            'd' => $book->description ?? '',
            'x' => $book->excerpt ?? '',
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Modifications enregistrées avec succès.',
                'book' => $formattedBook,
            ]);
        }

        return redirect()->route('admin.index')->with('success', 'Modifications enregistrées avec succès.');
    }

    /**
     * Remove the specified book from storage.
     */
    public function destroy(Request $request, Book $book): JsonResponse|RedirectResponse
    {
        $book->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ouvrage retiré du catalogue.',
            ]);
        }

        return redirect()->route('admin.index')->with('success', 'Ouvrage retiré du catalogue.');
    }

    /**
     * Generate a unique slug for a book.
     */
    protected function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (Book::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }
}
