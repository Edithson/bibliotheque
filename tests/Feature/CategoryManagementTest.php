<?php

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('gerant can create a category with a unique name', function () {
    $gerant = User::factory()->gerant()->create();

    $response = $this->actingAs($gerant)->post('/admin/categories', [
        'name' => 'Philosophie Antique',
    ]);

    $response->assertRedirect('/admin/categories');
    $this->assertDatabaseHas('categories', [
        'name' => 'Philosophie Antique',
        'slug' => 'philosophie-antique',
    ]);
});

test('creating a category with a duplicate name cancels creation and returns error message', function () {
    $gerant = User::factory()->gerant()->create();
    Category::create(['name' => 'Sciences', 'slug' => 'sciences']);

    // Attempt duplicate creation (case-insensitive)
    $response = $this->actingAs($gerant)->post('/admin/categories', [
        'name' => 'sciences',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');

    // Ensure only 1 category exists
    expect(Category::where('slug', 'sciences')->count())->toBe(1);
});

test('updating a category to a duplicate name is blocked', function () {
    $gerant = User::factory()->gerant()->create();
    $cat1 = Category::create(['name' => 'Histoire', 'slug' => 'histoire']);
    $cat2 = Category::create(['name' => 'Géographie', 'slug' => 'geographie']);

    // Attempt updating cat2 to 'histoire'
    $response = $this->actingAs($gerant)->put("/admin/categories/{$cat2->id}", [
        'name' => 'Histoire',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');

    expect($cat2->fresh()->name)->toBe('Géographie');
});

test('deleting an empty category succeeds', function () {
    $gerant = User::factory()->gerant()->create();
    $category = Category::create(['name' => 'Biographies', 'slug' => 'biographies']);

    $response = $this->actingAs($gerant)->delete("/admin/categories/{$category->id}");

    $response->assertRedirect('/admin/categories');
    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

test('deleting a category that contains books is blocked with an error message', function () {
    $gerant = User::factory()->gerant()->create();
    $category = Category::create(['name' => 'Romans', 'slug' => 'romans']);

    Book::create([
        'title' => 'Livre dans la Catégorie',
        'slug' => 'livre-dans-la-categorie',
        'author' => 'Auteur',
        'category_id' => $category->id,
        'price' => 1000,
    ]);

    $response = $this->actingAs($gerant)->delete("/admin/categories/{$category->id}");

    $response->assertRedirect('/admin/categories');
    $response->assertSessionHas('error');

    // Category must NOT be deleted
    $this->assertDatabaseHas('categories', ['id' => $category->id]);
});
