<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('unauthenticated user is redirected to login when trying to access profile', function () {
    $this->get('/profile')->assertRedirect('/login');
});

test('authenticated user can view their profile edit page', function () {
    $user = User::factory()->guest()->create(['name' => 'Jean Valjean']);

    $response = $this->actingAs($user)->get('/profile');
    $response->assertStatus(200);
    $response->assertSee('Jean Valjean');
    $response->assertSee('Gestion de Mon Compte');
});

test('authenticated user can update their profile information', function () {
    $user = User::factory()->guest()->create([
        'name' => 'Ancien Nom',
        'email' => 'ancien.email@example.com',
    ]);

    $response = $this->actingAs($user)->put('/profile', [
        'name' => 'Nouveau Nom',
        'email' => 'nouveau.email@example.com',
    ]);

    $response->assertRedirect('/profile');
    $response->assertSessionHas('success');

    $user->refresh();
    expect($user->name)->toBe('Nouveau Nom');
    expect($user->email)->toBe('nouveau.email@example.com');
});

test('authenticated user can update their password', function () {
    $user = User::factory()->guest()->create([
        'password' => Hash::make('old-password-123'),
    ]);

    $response = $this->actingAs($user)->put('/profile/password', [
        'current_password' => 'old-password-123',
        'password' => 'new-secure-password',
        'password_confirmation' => 'new-secure-password',
    ]);

    $response->assertRedirect('/profile');
    $response->assertSessionHas('success');

    $user->refresh();
    expect(Hash::check('new-secure-password', $user->password))->toBeTrue();
});

test('profile page uses admin layout for back-office users (author, gerant, admin)', function () {
    $this->seed(DatabaseSeeder::class);

    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get('/profile');
    $response->assertStatus(200);
    $response->assertSee('Registre des Livres');
});

test('profile page uses guest layout for regular guest users', function () {
    $this->seed(DatabaseSeeder::class);

    $guest = User::factory()->guest()->create();

    $response = $this->actingAs($guest)->get('/profile');
    $response->assertStatus(200);
    $response->assertSee('Retour aux Rayons');
});

test('non-admin user can delete their account with password confirmation', function () {
    $this->seed(DatabaseSeeder::class);

    $user = User::factory()->guest()->create([
        'password' => Hash::make('password123'),
    ]);

    $response = $this->actingAs($user)->delete('/profile', [
        'password' => 'password123',
    ]);

    $response->assertRedirect('/');
    $response->assertSessionHas('success');

    $this->assertGuest();
    $this->assertSoftDeleted('users', ['id' => $user->id]);
});

test('non-admin user cannot delete account with incorrect password', function () {
    $this->seed(DatabaseSeeder::class);

    $user = User::factory()->guest()->create([
        'password' => Hash::make('password123'),
    ]);

    $response = $this->actingAs($user)->delete('/profile', [
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertDatabaseHas('users', ['id' => $user->id]);
});

test('admin user cannot delete their own account', function () {
    $this->seed(DatabaseSeeder::class);

    $admin = User::factory()->admin()->create([
        'password' => Hash::make('password123'),
    ]);

    $response = $this->actingAs($admin)->delete('/profile', [
        'password' => 'password123',
    ]);

    $response->assertRedirect('/profile');
    $response->assertSessionHas('error');
    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});
