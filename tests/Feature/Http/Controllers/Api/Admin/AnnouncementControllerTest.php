<?php

namespace Tests\Feature\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AnnouncementControllerTest extends TestCase
{
    use WithFaker;

    /**
     * Test Add Announcement
     * 
     * @return void
     */
    public function testAddAnnouncement()
    {
        auth()->login((User::find(1)));
        $announcement = [
            'name' => $this->faker->name,
            'message' => $this->faker->text,
            'to' => rand(1, 4)
        ];

        $response = $this->post(route('admin.announcements.store'), $announcement);

        $response->assertJson([
            'status' => 200,
            'message' => 'Announcement Created Successfully'
        ]);
    }

    /**
     * Test Get All Announcements
     * 
     * @return void
     */
    public function testGetAllAnnouncements()
    {
        auth()->login((User::find(1)));
        $response = $this->get(route('admin.announcements.index'));

        $response->assertJson([
            'status' => 200,
            'message' => 'Success',
            'data' => []
        ]);
    }

    /**
     * Test Get Announcement By Id
     * 
     * @return void
     */
    public function testGetAnnouncementById()
    {
        auth()->login((User::find(1)));
        $response = $this->get(route('admin.announcements.show', 1));

        $response->assertJson([
            'status' => 200,
            'message' => 'Success',
            'data' => []
        ]);
    }

    /**
     * Test Update Announcement
     * 
     * @return void
     */
    public function testUpdateAnnouncement()
    {
        auth()->login((User::find(1)));
        $announcement = [
            'name' => $this->faker->name,
            'message' => $this->faker->text,
            'to' => rand(1, 4)
        ];

        $response = $this->put(route('admin.announcements.update', 1), $announcement);

        $response->assertJson([
            'status' => 200,
            'message' => 'Announcement Updated Successfully'
        ]);
    }

    /**
     * Test Delete Announcement
     * 
     * @return void
     */
    public function testDeleteAnnouncement()
    {
        auth()->login((User::find(1)));
        $response = $this->delete(route('admin.announcements.destroy', 1));

        $response->assertJson([
            'status' => 200,
            'message' => 'Announcement Deleted Successfully'
        ]);
    }
}
