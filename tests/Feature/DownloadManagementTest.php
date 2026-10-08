<?php

use App\Models\Book;
use App\Models\Category;
use App\Models\Download;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('unauthorized guest user cannot access admin downloads index or export', function () {
    $guest = User::factory()->guest()->create();

    $this->actingAs($guest)->get('/admin/downloads')->assertStatus(403);
    $this->actingAs($guest)->get('/admin/downloads/export')->assertStatus(403);
});

test('gerant and admin users can access admin downloads index with analytics KPIs', function () {
    $gerant = User::factory()->gerant()->create();
    $category = Category::first();

    $book = Book::create([
        'title' => 'Livre Populaire',
        'slug' => 'livre-populaire-1',
        'author' => 'Auteur Exemple',
        'category_id' => $category->id,
        'price' => 0,
        'is_published' => true,
    ]);

    Download::create([
        'book_id' => $book->id,
        'user_id' => $gerant->id,
        'ip_address' => '127.0.0.1',
    ]);

    $response = $this->actingAs($gerant)->get('/admin/downloads');
    $response->assertStatus(200);
    $response->assertSee('Registre des Téléchargements');
    $response->assertSee('Livre Populaire');
    $response->assertSee($gerant->name);
});

test('gerant or admin user can filter downloads by search term and price type', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    $freeBook = Book::create([
        'title' => 'Gratuit Roman',
        'slug' => 'gratuit-roman',
        'author' => 'Auteur Gratuit',
        'category_id' => $category->id,
        'price' => 0,
        'is_published' => true,
    ]);

    $paidBook = Book::create([
        'title' => 'Payant Roman',
        'slug' => 'payant-roman',
        'author' => 'Auteur Payant',
        'category_id' => $category->id,
        'price' => 5000,
        'is_published' => true,
    ]);

    Download::create(['book_id' => $freeBook->id, 'user_id' => $admin->id]);
    Download::create(['book_id' => $paidBook->id, 'user_id' => $admin->id]);

    // Search filter
    $responseSearch = $this->actingAs($admin)->get('/admin/downloads?search=Gratuit');
    $responseSearch->assertStatus(200);
    expect($responseSearch->viewData('downloads')->pluck('book.title'))->toContain('Gratuit Roman');
    expect($responseSearch->viewData('downloads')->pluck('book.title'))->not->toContain('Payant Roman');

    // Price type filter
    $responsePaid = $this->actingAs($admin)->get('/admin/downloads?price_type=paid');
    $responsePaid->assertStatus(200);
    expect($responsePaid->viewData('downloads')->pluck('book.title'))->toContain('Payant Roman');
    expect($responsePaid->viewData('downloads')->pluck('book.title'))->not->toContain('Gratuit Roman');
});

test('gerant or admin user can export downloads to CSV file', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    $book = Book::create([
        'title' => 'Ouvrage Exportable',
        'slug' => 'ouvrage-exportable',
        'author' => 'Auteur Export',
        'category_id' => $category->id,
        'price' => 2500,
        'is_published' => true,
    ]);

    Download::create([
        'book_id' => $book->id,
        'user_id' => $admin->id,
        'ip_address' => '192.168.1.1',
    ]);

    $response = $this->actingAs($admin)->get('/admin/downloads/export');

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    $this->assertTrue(str_contains($response->headers->get('Content-Disposition'), 'export-telechargements-'));

    $content = $response->streamedContent();
    expect($content)->toContain('Ouvrage Exportable');
    expect($content)->toContain('Auteur Export');
    expect($content)->toContain('192.168.1.1');
});
