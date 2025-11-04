<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('sends a reset password notification to registered users', function (): void {
    Notification::fake();

    $user = User::factory()->create([
        'email' => 'tony@example.com',
    ]);

    $response = $this->postJson('/api/auth/forgot-password', [
        'email' => 'tony@example.com',
    ]);

    $response->assertOk()
        ->assertJson(fn ($json) => $json->has('status'));

    Notification::assertSentTo($user, ResetPassword::class);
});

it('responds with validation errors for unknown email addresses', function (): void {
    $response = $this->postJson('/api/auth/forgot-password', [
        'email' => 'missing@example.com',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});
