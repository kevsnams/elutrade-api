<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EmailSignupTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testMissingAll()
    {
        $response = $this->postJson('api/v1/signup/email', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'email', 'password', 'password_confirm', 'first_name', 'last_name', 'agreed_terms'
        ]);
    }

    public function testExistingEmail()
    {
        $existingUser = User::factory()->create();

        $response = $this->postJson('api/v1/signup/email', [
            'email' => $existingUser->email,
            'password' => 'password',
            'password_confirm' => 'password',
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'agreed_terms' => true
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'email'
        ]);
    }

    public function testIncorrectEmailFormat()
    {
        $response = $this->postJson('api/v1/signup/email', [
            'email' => 'wrong.email#@#@',
            'password' => 'password',
            'password_confirm' => 'wordpass',
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'agreed_terms' => true
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'email'
        ]);
    }

    public function testPasswordMismatch()
    {
        $response = $this->postJson('api/v1/signup/email', [
            'email' => $this->faker->safeEmail,
            'password' => 'password',
            'password_confirm' => 'wordpass',
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'agreed_terms' => true
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'password_confirm'
        ]);
    }

    public function testCorrectSignup()
    {
        $newUser = [
            'email' => $this->faker->safeEmail,
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->lastName,
            'last_name' => $this->faker->lastName,
        ];

        $response = $this->postJson('api/v1/signup/email', array_merge(
            $newUser,
            [
                'password' => 'password',
                'password_confirm' => 'password',
                'agreed_terms' => true
            ]
        ));

        $response->assertSuccessful();
        $response->assertJson(['success' => true], true);

        $this->assertDatabaseHas('users', $newUser);
    }

    public function testDisagreedTerms()
    {
        $response = $this->postJson('api/v1/signup/email', array_merge(
            [
                'email' => $this->faker->safeEmail,
                'password' => 'password',
                'password_confirm' => 'password',
                'first_name' => $this->faker->firstName,
                'middle_name' => $this->faker->lastName,
                'last_name' => $this->faker->lastName,
                'agreed_terms' => false
            ]
        ));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'agreed_terms'
        ]);
    }
}
