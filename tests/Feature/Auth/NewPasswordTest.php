<?php

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

it('resets the user password and revokes access tokens', function (): void {
    Event::fake([PasswordReset::class]);

    $user = User::factory()->create([
        'email' => 'riley@example.com',
    ]);

    $user->createToken('legacy-token');

    $token = Password::createToken($user);

    $response = $this->postJson('/api/auth/reset-password', [
        'email' => 'riley@example.com',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
        'token' => $token,
    ]);

    $response->assertOk()
        ->assertJson(fn ($json) => $json->has('status'));

    $user->refresh();

    expect(Hash::check('new-password', $user->password))->toBeTrue();
    expect($user->tokens)->toHaveCount(0);

    Event::assertDispatched(PasswordReset::class);
});

it('rejects invalid reset tokens', function (): void {
    $user = User::factory()->create([
        'email' => 'ruby@example.com',
    ]);

    $response = $this->postJson('/api/auth/reset-password', [
        'email' => 'ruby@example.com',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
        'token' => 'invalid-token',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});
