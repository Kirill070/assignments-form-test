<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_page_renders_with_required_fields(): void
    {
        $response = $this->get('/users/create');

        $response->assertStatus(200);
        $response->assertSee('name="first_name"', false);
        $response->assertSee('name="last_name"', false);
        $response->assertSee('name="email"', false);
        $response->assertSee('name="password"', false);
        $response->assertSee('name="password_confirmation"', false);
    }

    public function test_can_submit_registration_form(): void
    {
        $payload = [
            'first_name' => 'Ivan',
            'last_name' => 'Petrov',
            'email' => 'new.user@example.test',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        $response = $this->postJson('/users', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonStructure([
            'success',
            'message',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'new.user@example.test',
            'first_name' => 'Ivan',
            'last_name' => 'Petrov',
        ]);

    }

    public function test_duplicate_email_returns_error(): void
    {
        User::factory()->create([
            'first_name' => 'Maria',
            'last_name' => 'Ivanova',
            'email' => 'maria.ivanova@example.test',
        ]);

        $payload = [
            'first_name' => 'Maria',
            'last_name' => 'Ivanova',
            'email' => 'maria.ivanova@example.test',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        $response = $this->postJson('/users', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'message' => 'User with this email already exists.',
        ]);
    }
}
