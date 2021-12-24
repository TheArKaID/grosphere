<?php

namespace Tests\Feature\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
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
        auth()->login((User::find(1)));
        $password = $this->faker->password(8);
        $parent = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber(),
            'password' => $password,
            'password_confirmation' => $password,
            'address' => $this->faker->address
        ];
        $response = $this->post(route('admin.parents.store'), $parent);

        unset($parent['password']);
        unset($parent['password_confirmation']);

        $response->assertJson([
            'status' => 200,
            'message' => 'Parent Created Successfully',
            'data' => $parent
        ]);
    }

    /**
     * Get All Parents Test
     * 
     * @return void
     */
    public function testGetAllParent()
    {
        auth()->login((User::find(1)));
        $response = $this->get(route('admin.parents.index'));

        $response->assertJson([
            'status' => 200,
            'message' => 'Success',
            'data' => []
        ]);
    }

    /**
     * Get Parent Test
     * 
     * @return void
     */
    public function testGetParent()
    {
        auth()->login((User::find(1)));

        $response = $this->get(route('admin.parents.show', 1));

        $response->assertJson([
            'status' => 200,
            'message' => 'Success',
            'data' => []
        ]);
    }

    /**
     * Update Parent Test
     * 
     * @return void
     */
    public function testUpdateParent()
    {
        auth()->login((User::find(1)));
        $parent = [
            'name' => $this->faker->name,
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address
        ];
        $response = $this->put(route('admin.parents.update', 1), $parent);

        $response->assertJson([
            'status' => 200,
            'message' => 'Parent Updated Successfully',
            'data' => $parent
        ]);
    }

    /**
     * Change Parent Password Test
     * 
     * @return void
     */
    public function testChangeParentPassword()
    {
        auth()->login((User::find(1)));
        $password = $this->faker->password(4). "1aA!";
        $parent = [
            'password' => $password,
            'password_confirmation' => $password
        ];
        $response = $this->put(route('admin.parents.change-password', 1), $parent);

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
        auth()->login((User::find(1)));
        $response = $this->delete(route('admin.parents.destroy', 1));

        $response->assertJson([
            'status' => 200,
            'message' => 'Parent Deleted Successfully'
        ]);
    }
}
