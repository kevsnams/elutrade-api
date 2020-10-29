<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class UserReadSingleTest extends BaseTestCase
{
    use RefreshDatabase;

    public function testUnAuthFetchShouldProceed()
    {
        $user = User::factory()->create();
        $http = $this->requestJsonApi('api/v1/users/'. $user->hash_id);

        $http['response']->assertSuccessful();

        $this->assertNotEmpty($http['json']['data']);
        $this->assertEquals($user->hash_id, $http['json']['data']['hash_id']);
        $this->assertEquals($user->first_name, $http['json']['data']['first_name']);
        $this->assertEquals($user->last_name, $http['json']['data']['last_name']);
        $this->assertEquals($user->full_name, $http['json']['data']['full_name']);
        $this->assertEquals($user->email, $http['json']['data']['email']);
    }

    public function testAuthFetchShouldProceed()
    {
        $user = User::factory()->create();
        $http = $this->requestJsonApi('api/v1/users/' . $user->hash_id);

        $http['response']->assertSuccessful();

        $this->assertNotEmpty($http['json']['data']);
        $this->assertEquals($user->hash_id, $http['json']['data']['hash_id']);
        $this->assertEquals($user->first_name, $http['json']['data']['first_name']);
        $this->assertEquals($user->last_name, $http['json']['data']['last_name']);
        $this->assertEquals($user->full_name, $http['json']['data']['full_name']);
        $this->assertEquals($user->email, $http['json']['data']['email']);
    }
}
