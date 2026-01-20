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

        $this->assertLogContains('email=new.user@example.test status=registered');
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

        $this->assertLogContains('email=maria.ivanova@example.test status=duplicate');
    }

    public function test_validation_errors_are_returned(): void
    {
        $payload = [
            'first_name' => '',
            'last_name' => '',
            'email' => 'invalid-email',
            'password' => 'secret123',
            'password_confirmation' => 'different',
        ];

        $response = $this->postJson('/users', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'message' => 'First name is required.',
        ]);
    }

    public function test_missing_password_returns_error(): void
    {
        $payload = [
            'first_name' => 'Alex',
            'last_name' => 'Ivanov',
            'email' => 'alex.ivanov@example.test',
            'password' => '',
            'password_confirmation' => '',
        ];

        $response = $this->postJson('/users', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'message' => 'Password is required.',
        ]);
    }

    public function test_invalid_email_returns_error(): void
    {
        $payload = [
            'first_name' => 'Olga',
            'last_name' => 'Smirnova',
            'email' => 'invalid-email',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        $response = $this->postJson('/users', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'message' => 'Email must contain "@".',
        ]);
    }

    public function test_password_mismatch_returns_error(): void
    {
        $payload = [
            'first_name' => 'Dmitry',
            'last_name' => 'Sokolov',
            'email' => 'dmitry.sokolov@example.test',
            'password' => 'secret123',
            'password_confirmation' => 'different',
        ];

        $response = $this->postJson('/users', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'message' => 'Passwords do not match.',
        ]);
    }

    protected function assertLogContains(string $expectedLine): void
    {
        $logPath = storage_path('logs/registration.log');

        $this->assertFileExists($logPath);
        $contents = file_get_contents($logPath);

        $this->assertNotFalse($contents);
        $this->assertStringContainsString($expectedLine, $contents);
    }
}
