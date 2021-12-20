<?php

namespace Tests\Feature\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ParentControllerTest extends TestCase
{
    use WithFaker;

    /**
     * Add Parent Test
     * 
     * @return void
     */
    public function testAddParent()
    {
        Sanctum::actingAs(
            User::find(1),
            ['*']
        );
        $password = $this->faker->password(8);
        $parent = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber(),
            'password' => $password,
            'password_confirmation' => $password,
            'address' => $this->faker->address
        ];
        $response = $this->post(route('parents.store'), $parent);

        unset($parent['password']);
        unset($parent['password_confirmation']);

        $response->assertJson([
            'status' => 200,
            'message' => 'Parent Created Successfully',
            'response' => $parent
        ]);
    }

    /**
     * Get All Parents Test
     * 
     * @return void
     */
    public function testGetAllParent()
    {
        Sanctum::actingAs(
            User::find(1),
            ['*']
        );
        $response = $this->get(route('parents.index'));

        $response->assertJson([
            'status' => 200,
            'message' => 'Success',
            'response' => []
        ]);
    }

    /**
     * Get Parent Test
     * 
     * @return void
     */
    public function testGetParent()
    {
        Sanctum::actingAs(
            User::find(1),
            ['*']
        );

        $response = $this->get(route('parents.show', 1));

        $response->assertJson([
            'status' => 200,
            'message' => 'Success',
            'response' => []
        ]);
    }

    /**
     * Update Parent Test
     * 
     * @return void
     */
    public function testUpdateParent()
    {
        Sanctum::actingAs(
            User::find(1),
            ['*']
        );
        $parent = [
            'name' => $this->faker->name,
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address
        ];
        $response = $this->put(route('parents.update', 1), $parent);

        $response->assertJson([
            'status' => 200,
            'message' => 'Parent Updated Successfully',
            'response' => $parent
        ]);
    }

    /**
     * Change Parent Password Test
     * 
     * @return void
     */
    public function testChangeParentPassword()
    {
        Sanctum::actingAs(
            User::find(1),
            ['*']
        );
        $password = $this->faker->password(4). "1aA!";
        $parent = [
            'password' => $password,
            'password_confirmation' => $password
        ];
        $response = $this->put(route('parents.change-password', 1), $parent);

        $response->assertJson([
            'status' => 200,
            'message' => 'Parent Password Changed Successfully'
        ]);
    }

    /**
     * Delete Parent Test
     * 
     * @return void
     */
    public function testDeleteParent()
    {
        Sanctum::actingAs(
            User::find(1),
            ['*']
        );
        $response = $this->delete('/api/admin/parents/1');

        $response->assertJson([
            'status' => 200,
            'message' => 'Parent Deleted Successfully'
        ]);
    }
}
