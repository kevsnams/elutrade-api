<?php

namespace Tests\Feature;

use Tests\TestCase;

class BaseTestCase extends TestCase
{
    public function requestJsonApi($url, $data = [], $verb = 'GET')
    {
        $response = $this->transformParams($url, $data, $verb);
        $json = $response->decodeResponseJson()->json();

        return compact('response', 'json');
    }

    public function requestUnAuth($url, $data = [], $verb = 'GET')
    {
        $response = $this->transformParams($url, $data, $verb);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.'
        ], true);
    }

    private function transformParams($url, $data, $verb)
    {
        $params = [];
        $verb = strtolower($verb);

        if ($verb === 'get') {
            $params = [$url . (empty($data) ? '' : '?' . http_build_query($data))];
        } else {
            $params = [$url, $data];
        }

        return call_user_func_array([$this, $verb . 'Json'], $params);
    }
}
