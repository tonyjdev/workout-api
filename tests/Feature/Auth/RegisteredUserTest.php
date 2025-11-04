<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('registers a new user and issues a token', function (): void {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'Casey Coach',
        'email' => 'casey@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'device_name' => 'phpunit',
    ]);

    $response->assertCreated()
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

    $user = User::where('email', 'casey@example.com')->first();

    expect($user)->not->toBeNull();
    expect($response->json('token_type'))->toBe('Bearer');
});

it('rejects duplicate email addresses', function (): void {
    User::factory()->create([
        'email' => 'raquel@example.com',
    ]);

    $response = $this->postJson('/api/auth/register', [
        'name' => 'Raquel Row',
        'email' => 'raquel@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});
