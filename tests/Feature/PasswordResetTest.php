<?php

use App\Models\User;
use App\Notifications\QueuedResetPassword;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('forgot password screen can be rendered', function () {
    $response = $this->get('/forgot-password');
    $response->assertStatus(200);
    $response->assertSee('Mot de Passe Oublié ?');
});

test('password reset link can be requested', function () {
    Notification::fake();

    $user = User::factory()->guest()->create(['email' => 'lecteur.oubli@example.com']);

    $response = $this->post('/forgot-password', [
        'email' => 'lecteur.oubli@example.com',
    ]);

    $response->assertSessionHas('status');

    Notification::assertSentTo(
        [$user],
        QueuedResetPassword::class
    );
});

test('reset password screen can be rendered with valid token', function () {
    $user = User::factory()->guest()->create(['email' => 'token.user@example.com']);
    $token = Password::createToken($user);

    $response = $this->get('/reset-password/'.$token.'?email='.$user->email);
    $response->assertStatus(200);
    $response->assertSee('Nouveau Mot de Passe');
});

test('password can be reset with valid token and user can log in', function () {
    $user = User::factory()->guest()->create([
        'email' => 'reset.user@example.com',
        'password' => Hash::make('old-password-123'),
    ]);

    $token = Password::createToken($user);

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => 'reset.user@example.com',
        'password' => 'new-password-456',
        'password_confirmation' => 'new-password-456',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHas('success');

    $user->refresh();
    expect(Hash::check('new-password-456', $user->password))->toBeTrue();

    // Verify user can log in with new password
    $loginResponse = $this->post('/login', [
        'email' => 'reset.user@example.com',
        'password' => 'new-password-456',
    ]);

    $loginResponse->assertRedirect('/');
    $this->assertAuthenticatedAs($user);
});
