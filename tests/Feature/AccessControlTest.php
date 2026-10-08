<?php

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->category = Category::create(['name' => 'Littérature', 'slug' => 'litterature']);
});

test('level 0 unauthenticated user access rights', function () {
    $book = Book::create([
        'title' => 'Livre Anonyme',
        'slug' => 'livre-anonyme',
        'author' => 'Auteur',
        'category_id' => $this->category->id,
        'price' => 0,
        'is_published' => true,
    ]);

    // Can view shop pages
    $this->get('/')->assertStatus(200);
    $this->get("/books/{$book->slug}")->assertStatus(200);

    // Downloading redirects to login preserving intended URL
    $this->get("/books/{$book->slug}/download")->assertRedirect('/login');

    // Accessing my-books redirects to login
    $this->get('/my-books')->assertRedirect('/login');

    // Admin access redirects to login
    $this->get('/admin')->assertRedirect('/login');
});

test('level 1 guest user access rights', function () {
    $guest = User::factory()->guest()->create();

    $book = Book::create([
        'title' => 'Livre Visiteur',
        'slug' => 'livre-visiteur',
        'author' => 'Auteur',
        'category_id' => $this->category->id,
        'price' => 1500,
        'is_published' => true,
    ]);

    // Can view my-books
    $this->actingAs($guest)->get('/my-books')->assertStatus(200);

    // Can download book
    $this->actingAs($guest)->get("/books/{$book->slug}/download")->assertStatus(200);

    // Forbidden on admin routes
    $this->actingAs($guest)->get('/admin')->assertStatus(403);
    $this->actingAs($guest)->get('/admin/books')->assertStatus(403);
});

test('level 2 author user access rights and restrictions', function () {
    $author = User::factory()->author()->create();
    $otherAuthor = User::factory()->author()->create();

    $ownUnpublishedBook = Book::create([
        'title' => 'Livre Auteur Brouillon',
        'slug' => 'livre-auteur-brouillon',
        'author' => $author->name,
        'user_id' => $author->id,
        'category_id' => $this->category->id,
        'price' => 1000,
        'is_published' => false,
    ]);

    $ownPublishedBook = Book::create([
        'title' => 'Livre Auteur Publie',
        'slug' => 'livre-auteur-publie',
        'author' => $author->name,
        'user_id' => $author->id,
        'category_id' => $this->category->id,
        'price' => 2000,
        'is_published' => true,
    ]);

    $otherBook = Book::create([
        'title' => 'Livre Autre Auteur',
        'slug' => 'livre-autre-auteur',
        'author' => $otherAuthor->name,
        'user_id' => $otherAuthor->id,
        'category_id' => $this->category->id,
        'price' => 3000,
        'is_published' => true,
    ]);

    // Can access admin books list & downloads
    $this->actingAs($author)->get('/admin/books')->assertStatus(200);
    $this->actingAs($author)->get('/admin/downloads')->assertStatus(200);

    // Can view own unpublished book show page and edit page
    $this->actingAs($author)->get("/admin/books/{$ownUnpublishedBook->id}")->assertStatus(200);
    $this->actingAs($author)->get("/admin/books/{$ownUnpublishedBook->id}/edit")->assertStatus(200);

    // Can update own unpublished book
    $this->actingAs($author)->put("/admin/books/{$ownUnpublishedBook->id}", [
        'title' => 'Livre Auteur Modifie',
        'category_id' => $this->category->id,
        'price' => 1200,
    ])->assertRedirect();

    // Cannot edit or update own PUBLISHED book (403 Forbidden)
    $this->actingAs($author)->get("/admin/books/{$ownPublishedBook->id}/edit")->assertStatus(403);
    $this->actingAs($author)->put("/admin/books/{$ownPublishedBook->id}", [
        'title' => 'Livre Modif Interdite',
        'author' => $author->name,
        'category_id' => $this->category->id,
        'price' => 2000,
    ])->assertStatus(403);

    // Cannot delete own PUBLISHED book (403 Forbidden)
    $this->actingAs($author)->delete("/admin/books/{$ownPublishedBook->id}")->assertStatus(403);

    // Cannot view, edit, update, or delete other author's book (403 Forbidden)
    $this->actingAs($author)->get("/admin/books/{$otherBook->id}")->assertStatus(403);
    $this->actingAs($author)->get("/admin/books/{$otherBook->id}/edit")->assertStatus(403);

    // Cannot access categories, contacts, or users (403 Forbidden)
    $this->actingAs($author)->get('/admin/categories')->assertStatus(403);
    $this->actingAs($author)->get('/admin/contacts')->assertStatus(403);
    $this->actingAs($author)->get('/admin/users')->assertStatus(403);
});

test('level 3 gerant user access rights', function () {
    $gerant = User::factory()->gerant()->create();

    $publishedBook = Book::create([
        'title' => 'Livre Publie',
        'slug' => 'livre-publie',
        'author' => 'Auteur X',
        'category_id' => $this->category->id,
        'price' => 2500,
        'is_published' => true,
    ]);

    // Can access books, categories, downloads
    $this->actingAs($gerant)->get('/admin/books')->assertStatus(200);
    $this->actingAs($gerant)->get('/admin/categories')->assertStatus(200);
    $this->actingAs($gerant)->get('/admin/downloads')->assertStatus(200);

    // Can edit published books
    $this->actingAs($gerant)->get("/admin/books/{$publishedBook->id}/edit")->assertStatus(200);

    // Forbidden from contacts and users (403 Forbidden)
    $this->actingAs($gerant)->get('/admin/contacts')->assertStatus(403);
    $this->actingAs($gerant)->get('/admin/users')->assertStatus(403);
});

test('level 4 admin user full access rights', function () {
    $admin = User::factory()->admin()->create();

    // Full access across all admin pages
    $this->actingAs($admin)->get('/admin/books')->assertStatus(200);
    $this->actingAs($admin)->get('/admin/categories')->assertStatus(200);
    $this->actingAs($admin)->get('/admin/downloads')->assertStatus(200);
    $this->actingAs($admin)->get('/admin/contacts')->assertStatus(200);
    $this->actingAs($admin)->get('/admin/users')->assertStatus(200);
});
