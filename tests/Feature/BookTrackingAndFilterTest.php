<?php

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('storing a book sets creator user_id and updated_by_user_id', function () {
    $user = User::factory()->gerant()->create();
    $category = Category::create(['name' => 'Essai', 'slug' => 'essai']);

    $response = $this->actingAs($user)->post('/admin/books', [
        'title' => 'Livre Traçabilité',
        'author' => 'Auteur Unique',
        'category_id' => $category->id,
        'price' => 2500,
        'is_published' => '1',
    ]);

    $response->assertRedirect('/admin');

    $book = Book::where('slug', 'livre-tracabilite')->firstOrFail();
    expect($book->user_id)->toBe($user->id);
    expect($book->updated_by_user_id)->toBe($user->id);
    expect($book->creator->id)->toBe($user->id);
});

test('updating a book sets updated_by_user_id to modifying user', function () {
    $creator = User::factory()->gerant()->create();
    $modifier = User::factory()->admin()->create();
    $category = Category::create(['name' => 'Essai', 'slug' => 'essai']);

    $book = Book::create([
        'title' => 'Livre Original',
        'slug' => 'livre-original',
        'author' => 'Auteur Initial',
        'category_id' => $category->id,
        'price' => 1000,
        'user_id' => $creator->id,
        'updated_by_user_id' => $creator->id,
    ]);

    $response = $this->actingAs($modifier)->put("/admin/books/{$book->id}", [
        'title' => 'Livre Modifié',
        'author' => 'Auteur Initial',
        'category_id' => $category->id,
        'price' => 1200,
    ]);

    $response->assertRedirect('/admin');

    $book->refresh();
    expect($book->user_id)->toBe($creator->id);
    expect($book->updated_by_user_id)->toBe($modifier->id);
    expect($book->updater->id)->toBe($modifier->id);
});

test('admin index allows searching by title author description or excerpt', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::create(['name' => 'Roman', 'slug' => 'roman']);

    $book1 = Book::create([
        'title' => 'Le Cartographe Mystérieux',
        'slug' => 'le-cartographe-mysterieux',
        'author' => 'Élise',
        'category_id' => $category->id,
        'price' => 5000,
        'description' => 'Un roman sur les étoiles',
        'user_id' => $admin->id,
    ]);

    $book2 = Book::create([
        'title' => 'Poésie Urbaine',
        'slug' => 'poesie-urbaine',
        'author' => 'Marc',
        'category_id' => $category->id,
        'price' => 0,
        'excerpt' => 'Dans les brumes de la ville...',
        'user_id' => $admin->id,
    ]);

    // Search by title
    $response = $this->actingAs($admin)->get('/admin/books?search=Cartographe');
    $response->assertSee('Le Cartographe Mystérieux');
    $response->assertDontSee('Poésie Urbaine');

    // Search by excerpt
    $response = $this->actingAs($admin)->get('/admin/books?search=brumes');
    $response->assertSee('Poésie Urbaine');
    $response->assertDontSee('Le Cartographe Mystérieux');
});

test('admin index allows filtering by category status and creator user', function () {
    $admin = User::factory()->admin()->create();
    $author = User::factory()->author()->create();

    $cat1 = Category::create(['name' => 'Histoire', 'slug' => 'histoire']);
    $cat2 = Category::create(['name' => 'Poésie', 'slug' => 'poesie']);

    $book1 = Book::create([
        'title' => 'Histoire Ancien',
        'slug' => 'histoire-ancien',
        'author' => 'Auteur 1',
        'category_id' => $cat1->id,
        'price' => 3000,
        'is_published' => true,
        'user_id' => $admin->id,
    ]);

    $book2 = Book::create([
        'title' => 'Poésie Moderne',
        'slug' => 'poesie-moderne',
        'author' => 'Auteur 2',
        'category_id' => $cat2->id,
        'price' => 0,
        'is_published' => false,
        'user_id' => $author->id,
    ]);

    // Filter by Category
    $response = $this->actingAs($admin)->get("/admin/books?category_id={$cat1->id}");
    $response->assertSee('Histoire Ancien');
    $response->assertDontSee('Poésie Moderne');

    // Filter by Status (hidden)
    $response = $this->actingAs($admin)->get('/admin/books?status=hidden');
    $response->assertSee('Poésie Moderne');
    $response->assertDontSee('Histoire Ancien');

    // Filter by Creator User
    $response = $this->actingAs($admin)->get("/admin/books?user_id={$author->id}");
    $response->assertSee('Poésie Moderne');
    $response->assertDontSee('Histoire Ancien');
});
