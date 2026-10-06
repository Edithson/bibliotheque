<?php

use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can list books on shop index page without errors', function () {
    $category = Category::create(['name' => 'Roman', 'slug' => 'roman']);
    Book::create([
        'title' => 'Test Book',
        'slug' => 'test-book',
        'author' => 'Author Test',
        'category_id' => $category->id,
        'price' => 5000,
        'stock' => 10,
    ]);

    $response = $this->get('/');
    $response->assertStatus(200);
});

test('can create a new book and generates a unique slug', function () {
    $category = Category::create(['name' => 'Roman', 'slug' => 'roman']);

    $payload = [
        'title' => 'L\'Ombre du vent',
        'author' => 'Carlos Ruiz Zafón',
        'category_id' => $category->id,
        'price' => 7500,
        'stock' => 5,
        'description' => 'Un roman gothique moderne à Barcelone.',
    ];

    $response = $this->postJson('/admin/books', $payload);

    $response->assertStatus(201);
    $response->assertJson([
        'success' => true,
    ]);

    $this->assertDatabaseHas('books', [
        'title' => 'L\'Ombre du vent',
        'slug' => 'lombre-du-vent',
        'price' => 7500,
    ]);
});

test('generates unique slugs for duplicate titles', function () {
    $category = Category::create(['name' => 'Roman', 'slug' => 'roman']);

    $payload = [
        'title' => 'Titre Doublon',
        'author' => 'Auteur 1',
        'category_id' => $category->id,
        'price' => 4000,
        'stock' => 3,
    ];

    $this->postJson('/admin/books', $payload)->assertStatus(201);
    $this->postJson('/admin/books', $payload)->assertStatus(201);

    $this->assertDatabaseHas('books', ['slug' => 'titre-doublon']);
    $this->assertDatabaseHas('books', ['slug' => 'titre-doublon-1']);
});

test('can update a book', function () {
    $category = Category::create(['name' => 'Roman', 'slug' => 'roman']);
    $book = Book::create([
        'title' => 'Original Title',
        'slug' => 'original-title',
        'author' => 'Author',
        'category_id' => $category->id,
        'price' => 3000,
        'stock' => 2,
    ]);

    $response = $this->putJson("/admin/books/{$book->id}", [
        'title' => 'Updated Title',
        'author' => 'Author',
        'category_id' => $category->id,
        'price' => 4500,
        'stock' => 8,
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('books', [
        'id' => $book->id,
        'title' => 'Updated Title',
        'slug' => 'updated-title',
        'price' => 4500,
        'stock' => 8,
    ]);
});

test('can delete a book', function () {
    $category = Category::create(['name' => 'Roman', 'slug' => 'roman']);
    $book = Book::create([
        'title' => 'To Delete',
        'slug' => 'to-delete',
        'author' => 'Author',
        'category_id' => $category->id,
        'price' => 2000,
        'stock' => 1,
    ]);

    $response = $this->deleteJson("/admin/books/{$book->id}");

    $response->assertStatus(200);
    $this->assertDatabaseMissing('books', ['id' => $book->id]);
});
