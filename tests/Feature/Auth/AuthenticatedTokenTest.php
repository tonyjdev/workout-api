<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('issues a personal access token with valid credentials', function (): void {
    $user = User::factory()->create([
        'password' => 'password',
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'phpunit',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'token',
            'token_type',
            'user' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ],
        ]);

    $user->refresh();

    expect($user->tokens)->toHaveCount(1);
    expect($response->json('token_type'))->toBe('Bearer');
});

it('rejects invalid credentials', function (): void {
    User::factory()->create([
        'email' => 'tester@example.com',
        'password' => 'password',
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'tester@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('revokes the current access token on logout', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('phpunit')->plainTextToken;

    $response = $this->withToken($token)->deleteJson('/api/auth/logout');

    $response->assertNoContent();

    $user->refresh();

    expect($user->tokens)->toHaveCount(0);
});
