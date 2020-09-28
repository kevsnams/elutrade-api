<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthMobileLoginTest extends TestCase
{
    use RefreshDatabase;

    public function testCorrectLogin()
    {
        $user = User::factory()->create();

        $response = $this->postJson('api/v1/auth', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'Xiaomi Pocophone F1'
        ]);

        $response->assertSuccessful();
        $decoded = $response->decodeResponseJson()->json();

        $this->assertArrayHasKey('success', $decoded);
        $this->assertArrayHasKey('access_token', $decoded);
    }

    public function testIncorrectLogin()
    {
        $response = $this->postJson('api/v1/auth', [
            'email' => 'nonexistent@emptydb.com',
            'password' => 'password',
            'device_name' => 'Hello World'
        ]);

        $response->assertJsonValidationErrors([
            'email'
        ]);
    }

    public function testExistingUserWithIncorrectPassword()
    {
        $user = User::factory()->create();

        $response = $this->postJson('api/v1/auth', [
            'email' => $user->email,
            'password' => 'notthepassword',
            'device_name' => 'Samsung'
        ]);

        $response->assertJsonValidationErrors([
            'email'
        ]);
    }
}
