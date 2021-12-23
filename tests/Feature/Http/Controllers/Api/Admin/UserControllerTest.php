<?php

namespace Tests\Feature\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetProfile()
    {
        auth()->login((User::find(1)));
        $response = $this->get('/api/profile');

        $response->assertJson([
            'status' => 200,
            'message' => 'success',
            'data' => [
                'name' => 'Admin',
                'email' => 'admin@gmail.com'
            ]
        ]);
    }
}
