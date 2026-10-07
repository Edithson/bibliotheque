<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can access users index page and search or filter users', function () {
    $admin = User::factory()->admin()->create(['name' => 'Admin Principal']);
    $user1 = User::factory()->author()->create(['name' => 'Auteur Molière', 'email' => 'moliere@example.com']);
    $user2 = User::factory()->guest()->create(['name' => 'Lecteur Corneille', 'email' => 'corneille@example.com']);

    $response = $this->actingAs($admin)->get('/admin/users');
    $response->assertStatus(200);
    $response->assertSee('Auteur Molière');
    $response->assertSee('Lecteur Corneille');

    // Search by name
    $response = $this->actingAs($admin)->get('/admin/users?search=Moli%C3%A8re');
    $response->assertSee('Auteur Molière');
    $response->assertDontSee('Lecteur Corneille');

    // Filter by type_id = 1 (Guest)
    $response = $this->actingAs($admin)->get('/admin/users?type_id=1');
    $response->assertSee('Lecteur Corneille');
    $response->assertDontSee('Auteur Molière');
});

test('admin can create a new user account', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post('/admin/users', [
        'name' => 'Nouveau Gérant',
        'email' => 'gerant.nouveau@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'type_id' => 3, // Gérant
    ]);

    $response->assertRedirect('/admin/users');
    $response->assertSessionHas('success');

    $newUser = User::where('email', 'gerant.nouveau@example.com')->first();
    expect($newUser)->not->toBeNull();
    expect($newUser->type_id)->toBe(3);
    expect($newUser->role)->toBe('gerant');
});

test('admin can update user details and role', function () {
    $admin = User::factory()->admin()->create();
    $targetUser = User::factory()->guest()->create(['name' => 'Ancien Lecteur', 'email' => 'ancien@example.com']);

    $response = $this->actingAs($admin)->put("/admin/users/{$targetUser->id}", [
        'name' => 'Nouvel Auteur',
        'email' => 'nouvel.auteur@example.com',
        'type_id' => 2, // Auteur
    ]);

    $response->assertRedirect('/admin/users');

    $targetUser->refresh();
    expect($targetUser->name)->toBe('Nouvel Auteur');
    expect($targetUser->email)->toBe('nouvel.auteur@example.com');
    expect($targetUser->type_id)->toBe(2);
});

test('admin can delete another user account', function () {
    $admin = User::factory()->admin()->create();
    $targetUser = User::factory()->guest()->create();

    $response = $this->actingAs($admin)->delete("/admin/users/{$targetUser->id}");

    $response->assertRedirect('/admin/users');
    $this->assertDatabaseMissing('users', ['id' => $targetUser->id]);
});

test('admin cannot delete their own account', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->delete("/admin/users/{$admin->id}");

    $response->assertRedirect('/admin/users');
    $response->assertSessionHas('error');
    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});

test('non-admin user cannot access user management endpoints', function () {
    $gerant = User::factory()->gerant()->create();

    $this->actingAs($gerant)->get('/admin/users')->assertStatus(403);
    $this->actingAs($gerant)->get('/admin/users/create')->assertStatus(403);
    $this->actingAs($gerant)->post('/admin/users', [])->assertStatus(403);
});
