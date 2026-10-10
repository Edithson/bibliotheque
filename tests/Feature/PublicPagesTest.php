<?php

use App\Models\Book;
use App\Models\Category;
use App\Models\Download;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('about page loads successfully', function () {
    $response = $this->get('/about');
    $response->assertStatus(200);
    $response->assertSee('À propos de la Bibliothèque');
});

test('contact page loads successfully with general subject by default', function () {
    $response = $this->get('/contact');
    $response->assertStatus(200);
    $response->assertSee('Nous Contacter');
});

test('contact page pre-fills author request message when subject parameter is provided', function () {
    $response = $this->get('/contact?subject=author_request');
    $response->assertStatus(200);
    $response->assertSee('Demande pour devenir Auteur');
    $response->assertSee('Je souhaite proposer mes ouvrages numériques');
});

test('user can submit contact form successfully', function () {
    $response = $this->post('/contact', [
        'name' => 'Auteur Candidat',
        'email' => 'candidat@example.com',
        'subject' => 'author_request',
        'message' => 'Bonjour, je souhaite proposer mes ouvrages numériques sur votre plateforme.',
    ]);

    $response->assertRedirect('/contact');
    $response->assertSessionHas('success');
});

test('my-books page redirects guest to login', function () {
    $response = $this->get('/my-books');
    $response->assertRedirect('/login');
});

test('my-books page displays acquired books for logged in user', function () {
    $user = User::factory()->guest()->create();
    $category = Category::create(['name' => 'Roman', 'slug' => 'roman']);

    $book = Book::create([
        'title' => 'Livre dans ma bibliothèque',
        'slug' => 'livre-ma-bibliotheque',
        'author' => 'Auteur',
        'category_id' => $category->id,
        'price' => 0,
        'is_published' => true,
    ]);

    Download::create([
        'book_id' => $book->id,
        'user_id' => $user->id,
        'ip_address' => '127.0.0.1',
    ]);

    $response = $this->actingAs($user)->get('/my-books');
    $response->assertStatus(200);
    $response->assertSee('Livre dans ma bibliothèque');
});

test('footer displays developer name and links to developer website from config', function () {
    config([
        'services.developer.name' => 'FONHOUO GAUS',
        'services.developer.url' => 'https://moafogaus.abrdns.com/',
    ]);

    $response = $this->get('/');
    $response->assertStatus(200);
    $response->assertSee('FONHOUO GAUS');
    $response->assertSee('https://moafogaus.abrdns.com/');
});

test('recaptcha and developer service configuration map environment variables correctly', function () {
    expect(config('services.recaptcha.site_key'))->not->toBeEmpty();
    expect(config('services.recaptcha.secret_key'))->not->toBeEmpty();
    expect(config('services.developer.name'))->toBe('FONHOUO GAUS');
    expect(config('services.developer.url'))->toBe('https://moafogaus.abrdns.com/');
});
