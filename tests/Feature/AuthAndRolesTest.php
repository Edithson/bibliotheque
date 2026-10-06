<?php

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user registration defaults role to guest', function () {
    $response = $this->post('/register', [
        'name' => 'Nouveau Lecteur',
        'email' => 'lecteur@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect('/');

    $user = User::where('email', 'lecteur@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe('guest');
    expect($user->role_level)->toBe(1);
    expect($user->isGuest())->toBeTrue();
});

test('unauthenticated user accessing admin is redirected to login', function () {
    $response = $this->get('/admin');
    $response->assertRedirect('/login');
});

test('guest role user receives 403 forbidden when accessing admin', function () {
    $guestUser = User::factory()->create(['role' => 'guest']);

    $response = $this->actingAs($guestUser)->get('/admin');
    $response->assertStatus(403);
});

test('author role user can access admin and submit book but is_published is forced to false', function () {
    $authorUser = User::factory()->create(['role' => 'auteur']);
    $category = Category::create(['name' => 'Poésie', 'slug' => 'poesie']);

    $response = $this->actingAs($authorUser)->get('/admin');
    $response->assertStatus(200);

    $payload = [
        'title' => 'Poème du Soir',
        'author' => 'Jean Ronsard',
        'category_id' => $category->id,
        'price' => 2000,
        'is_published' => '1', // Author tries to publish
    ];

    $response = $this->actingAs($authorUser)->post('/admin/books', $payload);
    $response->assertRedirect('/admin');

    $book = Book::where('slug', 'poeme-du-soir')->firstOrFail();
    expect($book->is_published)->toBeFalse(); // Must be false until validated by Gerant/Admin
});

test('author role user cannot access categories or users admin management', function () {
    $authorUser = User::factory()->create(['role' => 'auteur']);

    $this->actingAs($authorUser)->get('/admin/categories')->assertStatus(403);
    $this->actingAs($authorUser)->get('/admin/users')->assertStatus(403);
});

test('gerant role user can manage categories and publish books but cannot manage users', function () {
    $gerantUser = User::factory()->create(['role' => 'gerant']);
    $category = Category::create(['name' => 'Essai', 'slug' => 'essai']);

    $book = Book::create([
        'title' => 'Livre En Attente',
        'slug' => 'livre-en-attente',
        'author' => 'Auteur',
        'category_id' => $category->id,
        'price' => 1000,
        'is_published' => false,
    ]);

    // Can access categories
    $this->actingAs($gerantUser)->get('/admin/categories')->assertStatus(200);

    // Can toggle publication
    $response = $this->actingAs($gerantUser)->patch("/admin/books/{$book->id}/toggle-publish");
    $response->assertRedirect();
    expect($book->fresh()->is_published)->toBeTrue();

    // Cannot access users management
    $this->actingAs($gerantUser)->get('/admin/users')->assertStatus(403);
});

test('admin role user can manage user roles', function () {
    $adminUser = User::factory()->create(['role' => 'admin']);
    $targetUser = User::factory()->create(['role' => 'guest']);

    $response = $this->actingAs($adminUser)->get('/admin/users');
    $response->assertStatus(200);
    $response->assertSee($targetUser->email);

    $response = $this->actingAs($adminUser)->patch("/admin/users/{$targetUser->id}/role", [
        'role' => 'gerant',
    ]);

    $response->assertRedirect();
    expect($targetUser->fresh()->role)->toBe('gerant');
});

test('downloading paid book requires login while free book allows guest', function () {
    $category = Category::create(['name' => 'Roman', 'slug' => 'roman']);

    $freeBook = Book::create([
        'title' => 'Livre Gratuit',
        'slug' => 'livre-gratuit-1',
        'author' => 'Auteur',
        'category_id' => $category->id,
        'price' => 0,
        'is_published' => true,
    ]);

    $paidBook = Book::create([
        'title' => 'Livre Payant',
        'slug' => 'livre-payant-1',
        'author' => 'Auteur',
        'category_id' => $category->id,
        'price' => 4500,
        'is_published' => true,
    ]);

    // Free book downloadable by guest
    $this->get("/books/{$freeBook->slug}/download")->assertStatus(200);

    // Paid book redirects guest to login
    $this->get("/books/{$paidBook->slug}/download")->assertRedirect('/login');

    // Paid book downloadable when logged in
    $user = User::factory()->create(['role' => 'guest']);
    $this->actingAs($user)->get("/books/{$paidBook->slug}/download")->assertStatus(200);
});

test('google oauth redirect route returns redirect response', function () {
    $response = $this->get('/auth/google');
    $response->assertStatus(302);
    expect($response->getTargetUrl())->toContain('accounts.google.com');
});
