<?php

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('can list books on shop index page and only shows categories with published books', function () {
    $activeCategory = Category::create(['name' => 'Roman', 'slug' => 'roman']);
    $emptyCategory = Category::create(['name' => 'SansLivre', 'slug' => 'sans-livre']);

    Book::create([
        'title' => 'Livre Test',
        'slug' => 'livre-test',
        'author' => 'Auteur Test',
        'category_id' => $activeCategory->id,
        'price' => 0,
        'is_published' => true,
    ]);

    $response = $this->get('/');
    $response->assertStatus(200);
    $response->assertSee('Roman');
    $response->assertDontSee('SansLivre');
});

test('admin index paginates books list', function () {
    $user = User::factory()->gerant()->create();
    $category = Category::create(['name' => 'Roman', 'slug' => 'roman']);

    for ($i = 1; $i <= 15; $i++) {
        Book::create([
            'title' => "Book {$i}",
            'slug' => "book-{$i}",
            'author' => 'Author',
            'category_id' => $category->id,
            'price' => 0,
            'is_published' => true,
        ]);
    }

    $response = $this->actingAs($user)->get('/admin');
    $response->assertStatus(200);
    $response->assertSee('Book 15');
});

test('admin create page loads successfully', function () {
    $user = User::factory()->gerant()->create();
    Category::create(['name' => 'Roman', 'slug' => 'roman']);

    $response = $this->actingAs($user)->get('/admin/books/create');
    $response->assertStatus(200);
    $response->assertSee('Ajouter un nouvel ouvrage numérique');
});

test('admin edit page loads successfully', function () {
    $user = User::factory()->gerant()->create();
    $category = Category::create(['name' => 'Roman', 'slug' => 'roman']);
    $book = Book::create([
        'title' => 'Livre à modifier',
        'slug' => 'livre-a-modifier',
        'author' => 'Auteur',
        'category_id' => $category->id,
        'price' => 5000,
    ]);

    $response = $this->actingAs($user)->get("/admin/books/{$book->id}/edit");
    $response->assertStatus(200);
    $response->assertSee('Livre à modifier');
});

test('can create a digital book with uploaded pdf file and detects page count', function () {
    $user = User::factory()->gerant()->create();
    Storage::fake('local');
    $category = Category::create(['name' => 'Roman', 'slug' => 'roman']);
    $file = UploadedFile::fake()->create('mon-livre.pdf', 500, 'application/pdf');

    $payload = [
        'title' => 'L\'Ombre du vent',
        'author' => 'Carlos Ruiz Zafón',
        'category_id' => $category->id,
        'price' => 0,
        'is_published' => true,
        'book_file' => $file,
        'description' => 'Un roman gothique moderne à Barcelone.',
    ];

    $response = $this->actingAs($user)->post('/admin/books', $payload);

    $response->assertRedirect('/admin');

    $book = Book::where('slug', 'lombre-du-vent')->firstOrFail();
    expect($book->file_path)->not->toBeEmpty();
    expect($book->nbr_pages)->toBeGreaterThan(0);
    Storage::disk('local')->assertExists($book->file_path);
});

test('rejects non-text file types such as zip or mp4', function () {
    $user = User::factory()->gerant()->create();
    $category = Category::create(['name' => 'Roman', 'slug' => 'roman']);
    $invalidFile = UploadedFile::fake()->create('virus.exe', 500, 'application/x-msdownload');

    $payload = [
        'title' => 'Fichier Interdit',
        'author' => 'Pirate',
        'category_id' => $category->id,
        'price' => 0,
        'book_file' => $invalidFile,
    ];

    $response = $this->actingAs($user)->post('/admin/books', $payload);
    $response->assertSessionHasErrors(['book_file']);
});

test('can download a free e-book and logs the download entry', function () {
    $category = Category::create(['name' => 'Roman', 'slug' => 'roman']);
    $book = Book::create([
        'title' => 'Livre Gratuit',
        'slug' => 'livre-gratuit',
        'author' => 'Auteur',
        'category_id' => $category->id,
        'price' => 0,
        'is_published' => true,
    ]);

    $user = User::factory()->guest()->create();
    $response = $this->actingAs($user)->get("/books/{$book->slug}/download");

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/pdf');
    $this->assertDatabaseHas('downloads', [
        'book_id' => $book->id,
    ]);
});

test('gerant can unpublish a book that is currently published', function () {
    $gerant = User::factory()->gerant()->create();
    $category = Category::create(['name' => 'Roman', 'slug' => 'roman']);
    $book = Book::create([
        'title' => 'Livre Déjà Publié',
        'slug' => 'livre-deja-publie',
        'author' => 'Auteur',
        'category_id' => $category->id,
        'price' => 1000,
        'is_published' => true,
    ]);

    expect($book->is_published)->toBeTrue();

    $response = $this->actingAs($gerant)->put("/admin/books/{$book->id}", [
        'title' => 'Livre Déjà Publié',
        'author' => 'Auteur',
        'category_id' => $category->id,
        'price' => 1000,
        'is_published' => '0',
    ]);

    $response->assertRedirect('/admin');
    expect($book->fresh()->is_published)->toBeFalse();
});

test('deleting a book deletes physical PDF file from storage and soft-deletes the record', function () {
    Storage::fake('local');
    $gerant = User::factory()->gerant()->create();
    $category = Category::create(['name' => 'Roman', 'slug' => 'roman']);

    $filePath = 'books/test.pdf';
    Storage::disk('local')->put($filePath, 'pdf content');

    $book = Book::create([
        'title' => 'Livre Supprimé',
        'slug' => 'livre-supprime',
        'author' => 'Auteur',
        'category_id' => $category->id,
        'price' => 0,
        'file_path' => $filePath,
        'is_published' => true,
    ]);

    Storage::disk('local')->assertExists($filePath);

    $response = $this->actingAs($gerant)->delete("/admin/books/{$book->id}");

    $response->assertRedirect('/admin');
    Storage::disk('local')->assertMissing($filePath);
    $this->assertSoftDeleted('books', ['id' => $book->id]);
});
