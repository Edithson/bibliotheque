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
use Illuminate\Support\Facades\Auth;
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
        $books = Book::with('category:id,name')->where('is_published', true)->get();

        // Ne sélectionner que les catégories ayant au moins un livre publié
        $categories = Category::select('id', 'name', 'slug')->whereHas('books', function ($query) {
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

        return view('home.pages.shop.index', [
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

        return view('admin.pages.books.index', [
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

        return view('admin.pages.books.create', compact('categories'));
    }

    /**
     * Store a newly created e-book in storage.
     */
    public function store(StoreBookRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();
        $validated['slug'] = $this->generateUniqueSlug($validated['title']);

        $user = Auth::user();
        // Si l'utilisateur est un simple auteur (niveau < 3), la publication reste en attente de validation
        if ($user && ! $user->hasRoleLevel(3)) {
            $validated['is_published'] = false;
        } else {
            $validated['is_published'] = $request->boolean('is_published', true);
        }

        unset($validated['book_file']);

        $book = Book::create($validated);

        if ($request->hasFile('book_file')) {
            $tempPath = $request->file('book_file')->store('temp', 'local');
            ProcessBookFileUploadJob::dispatchSync($book, $tempPath);
        }

        $message = ($user && ! $user->hasRoleLevel(3))
            ? "L'ouvrage « {$book->title} » a été soumis et est en attente de validation par un gérant."
            : "L'ouvrage « {$book->title} » a été créé avec succès.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'book' => $book,
            ], 201);
        }

        return redirect()->route('admin.index')->with('success', $message);
    }

    /**
     * Show dedicated form page for editing an existing e-book.
     */
    public function edit(Book $book): View
    {
        $categories = Category::all();

        return view('admin.pages.books.edit', compact('book', 'categories'));
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

        $user = Auth::user();
        if ($user && ! $user->hasRoleLevel(3)) {
            // Auteur ne peut pas auto-valider s'il modifie
            $validated['is_published'] = false;
        } else {
            $validated['is_published'] = $request->boolean('is_published', $book->is_published);
        }

        unset($validated['book_file']);

        $book->update($validated);

        if ($request->hasFile('book_file')) {
            $tempPath = $request->file('book_file')->store('temp', 'local');
            ProcessBookFileUploadJob::dispatchSync($book, $tempPath);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Modifications enregistrées.',
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
     * Toggle publication status of an e-book (Gérant & Admin).
     */
    public function togglePublish(Book $book): RedirectResponse
    {
        $book->update(['is_published' => ! $book->is_published]);

        $status = $book->is_published ? 'publié' : 'masqué';

        return redirect()->back()->with('success', "L'ouvrage « {$book->title} » est désormais {$status}.");
    }

    /**
     * Download free e-book file.
     */
    public function download(Request $request, Book $book): RedirectResponse|StreamedResponse|BinaryFileResponse
    {
        if ($book->price !== 0 && ! auth()->check()) {
            return redirect()->route('login')->with('info', 'Veuillez vous connecter pour télécharger cet ouvrage.');
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
