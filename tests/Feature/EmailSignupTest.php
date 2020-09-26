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

    /**
     * @TODO Match the tests here as describe on README.md
     */
    public function testPasswordsMismatch()
    {
        $response = $this->postJson('api/v1/signup/email', [
            'email' => $this->faker->safeEmail,
            'password' => 'password',
            'password_repeat' => 'no_match',
            'agreed_terms' => true
        ]);

        $response->assertJsonValidationErrors([
            'password_repeat'
        ]);
    }

    public function testDisagreedTerms()
    {
        $response = $this->postJson('api/v1/signup/email', [
            'email' => $this->faker->safeEmail,
            'password' => 'password',
            'password_repeat' => 'password',
            'agreed_terms' => false
        ]);

        $response->assertJsonValidationErrors([
            'agreed_terms'
        ]);
    }

    public function testDuplicateEmail()
    {
        $user = User::factory()->create();

        $response = $this->postJson('api/v1/signup/email', [
            'email' => $user->email,
            'password' => 'password',
            'password_repeat' => 'password',
            'agreed_terms' => true
        ]);

        $response->assertJsonValidationErrors([
            'email'
        ]);
    }

    public function testSuccessfulSignup()
    {
        $email = $this->faker->safeEmail;

        $response = $this->postJson('api/v1/signup/email', [
            'email' => $email,
            'password' => 'password',
            'password_repeat' => 'password',
            'agreed_terms' => true
        ]);

        $response->assertSuccessful();
        $response->assertJson([
            'success' => true
        ], true);

        $this->assertDatabaseHas('users', [
            'email' => $email
        ]);
    }
}
