<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthUserRequestTest extends TestCase
{
    use RefreshDatabase;

    public function testAuthorizedFetch()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson('api/v1/auth/user');
        $response->assertSuccessful();

        $decoded = $response->decodeResponseJson()->json();

        $this->assertArrayHasKey('success', $decoded);
        $this->assertArrayHasKey('user', $decoded);
        $this->assertIsArray($decoded['user']);

        $this->assertEquals($user->hash_id, $decoded['user']['hash_id']);
    }

    public function testUnauthorizedFetch()
    {
        $response = $this->getJson('api/v1/auth/user');

        $response->assertStatus(401);

        $decoded = $response->decodeResponseJson()->json();

        $this->assertArrayHasKey('message', $decoded);
        $this->assertEquals('Unauthenticated.', $decoded['message']);
    }
}
