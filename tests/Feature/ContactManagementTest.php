<?php

use App\Mail\ContactSubmittedMail;
use App\Models\Contact;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('public user can submit contact form, saving to database and queuing email to admin', function () {
    Mail::fake();

    $response = $this->post('/contact', [
        'name' => 'Victor Hugo',
        'email' => 'victor.hugo@example.com',
        'subject' => 'author_request',
        'message' => 'Bonjour, je souhaite vous proposer de publier Les Misérables.',
    ]);

    $response->assertRedirect('/contact');
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('contacts', [
        'name' => 'Victor Hugo',
        'email' => 'victor.hugo@example.com',
        'subject' => 'author_request',
        'is_read' => false,
    ]);

    Mail::assertQueued(ContactSubmittedMail::class, function ($mail) {
        return $mail->hasTo('moafogaus@gmail.com') &&
               $mail->contact->name === 'Victor Hugo';
    });
});

test('unauthorized users (guest or simple author) cannot access back-office contact messages', function () {
    $author = User::factory()->author()->create();

    $this->actingAs($author)->get('/admin/contacts')->assertStatus(403);
});

test('gerant and admin users can access back-office contact messages', function () {
    $gerant = User::factory()->gerant()->create();
    $admin = User::factory()->admin()->create();

    Contact::create([
        'name' => 'Alexandre Dumas',
        'email' => 'dumas@example.com',
        'subject' => 'general',
        'message' => 'Le Comte de Monte-Cristo est prêt.',
    ]);

    $responseGerant = $this->actingAs($gerant)->get('/admin/contacts');
    $responseGerant->assertStatus(200);
    $responseGerant->assertSee('Alexandre Dumas');

    $responseAdmin = $this->actingAs($admin)->get('/admin/contacts');
    $responseAdmin->assertStatus(200);
    $responseAdmin->assertSee('Alexandre Dumas');
});

test('viewing a contact message detail marks it as read', function () {
    $admin = User::factory()->admin()->create();

    $contact = Contact::create([
        'name' => 'Émile Zola',
        'email' => 'zola@example.com',
        'subject' => 'support',
        'message' => 'J\'accuse ! Problème de téléchargement.',
        'is_read' => false,
    ]);

    expect($contact->is_read)->toBeFalse();

    $response = $this->actingAs($admin)->get('/admin/contacts/'.$contact->id);
    $response->assertStatus(200);
    $response->assertSee('Émile Zola');
    $response->assertSee('J\'accuse ! Problème de téléchargement.');

    $contact->refresh();
    expect($contact->is_read)->toBeTrue();
});

test('gerant or admin user can delete a contact message', function () {
    $admin = User::factory()->admin()->create();

    $contact = Contact::create([
        'name' => 'Honoré de Balzac',
        'email' => 'balzac@example.com',
        'subject' => 'general',
        'message' => 'La Comédie Humaine.',
    ]);

    $response = $this->actingAs($admin)->delete('/admin/contacts/'.$contact->id);
    $response->assertRedirect('/admin/contacts');
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
});
