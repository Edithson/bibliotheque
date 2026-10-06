<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Jobs\ProcessBookFileUploadJob;
use App\Models\Book;
use App\Models\Category;
use App\Models\Download;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookController extends Controller
{
    /**
     * Display public boutique interface.
     */
    public function shopIndex(): View
    {
        $books = Book::with('category')->where('is_published', true)->get();

        // Ne sélectionner que les catégories ayant au moins un livre publié
        $categories = Category::whereHas('books', function ($query) {
            $query->where('is_published', true);
        })->get();

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
            'd' => $b->description ?? '',
            'x' => $b->excerpt ?? ($b->description ?? ''),
            'file_path' => $b->file_path,
            'nbr_pages' => $b->nbr_pages,
            'download_url' => route('books.download', $b->slug),
        ])->values()->all();

        return view('shop.index', [
            'books' => $formattedBooks,
            'categories' => $categories,
        ]);
    }

    /**
     * Display administration ledger interface with pagination.
     */
    public function adminIndex(): View
    {
        $books = Book::with('category')->orderBy('id', 'desc')->paginate(10);
        $categories = Category::all();

        $totalBooks = Book::count();
        $freeBooks = Book::where('price', 0)->count();
        $paidBooks = Book::where('price', '>', 0)->count();
        $publishedBooks = Book::where('is_published', true)->count();

        return view('admin.index', [
            'books' => $books,
            'categories' => $categories,
            'totalBooks' => $totalBooks,
            'freeBooks' => $freeBooks,
            'paidBooks' => $paidBooks,
            'publishedBooks' => $publishedBooks,
        ]);
    }

    /**
     * Show dedicated form page for creating a new e-book.
     */
    public function create(): View
    {
        $categories = Category::all();

        return view('admin.books.create', compact('categories'));
    }

    /**
     * Store a newly created e-book in storage.
     */
    public function store(StoreBookRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();
        $validated['slug'] = $this->generateUniqueSlug($validated['title']);
        $validated['is_published'] = $request->boolean('is_published', true);

        // Retirer book_file du tableau de données brutes Eloquent
        unset($validated['book_file']);

        $book = Book::create($validated);

        if ($request->hasFile('book_file')) {
            $tempPath = $request->file('book_file')->store('temp', 'local');
            // Traiter via le job mis en queue
            ProcessBookFileUploadJob::dispatchSync($book, $tempPath);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ouvrage numérique créé avec succès.',
                'book' => $book,
            ], 201);
        }

        return redirect()->route('admin.index')->with('success', "L'ouvrage « {$book->title} » a été ajouté au catalogue.");
    }

    /**
     * Show dedicated form page for editing an existing e-book.
     */
    public function edit(Book $book): View
    {
        $categories = Category::all();

        return view('admin.books.edit', compact('book', 'categories'));
    }

    /**
     * Update the specified e-book in storage.
     */
    public function update(UpdateBookRequest $request, Book $book): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        if ($book->title !== $validated['title']) {
            $validated['slug'] = $this->generateUniqueSlug($validated['title'], $book->id);
        }

        $validated['is_published'] = $request->boolean('is_published', true);
        unset($validated['book_file']);

        $book->update($validated);

        if ($request->hasFile('book_file')) {
            $tempPath = $request->file('book_file')->store('temp', 'local');
            ProcessBookFileUploadJob::dispatchSync($book, $tempPath);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Modifications de l\'ouvrage enregistrées.',
                'book' => $book,
            ]);
        }

        return redirect()->route('admin.index')->with('success', "Modifications enregistrées pour « {$book->title} ».");
    }

    /**
     * Remove the specified e-book from storage.
     */
    public function destroy(Request $request, Book $book): JsonResponse|RedirectResponse
    {
        if ($book->file_path && Storage::disk('local')->exists($book->file_path)) {
            Storage::disk('local')->delete($book->file_path);
        }

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
     * Download free e-book file.
     */
    public function download(Request $request, Book $book): StreamedResponse|BinaryFileResponse
    {
        if ($book->price !== 0 && ! auth()->check()) {
            abort(403, 'Cet ouvrage n\'est pas disponible au téléchargement gratuit.');
        }

        Download::create([
            'book_id' => $book->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
        ]);

        if ($book->file_path && Storage::disk('local')->exists($book->file_path)) {
            return Storage::disk('local')->download($book->file_path, "{$book->slug}.pdf");
        }

        return response()->streamDownload(function () use ($book) {
            echo "==================================================\n";
            echo "  LA BIBLIOTHÈQUE DES MOTS - EXEMPLAIRE NUMÉRIQUE  \n";
            echo "==================================================\n\n";
            echo "Titre       : {$book->title}\n";
            echo "Auteur      : {$book->author}\n";
            echo 'Catégorie   : '.($book->category ? $book->category->name : 'Général')."\n";
            echo "Année       : {$book->publish_year}\n";
            echo "Pages       : {$book->nbr_pages}\n\n";
            echo "--------------------------------------------------\n";
            echo "SYNOPSIS :\n{$book->description}\n";
            echo "--------------------------------------------------\n\n";
            echo "EXTRAIT NUMÉRIQUE :\n".str_replace('|', "\n\n", $book->excerpt ?? '')."\n";
        }, "{$book->slug}.pdf", [
            'Content-Type' => 'application/pdf',
        ]);
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
