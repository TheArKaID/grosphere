<?php

namespace Tests;

use App\Models\Parents;
use App\Models\Student;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

trait MigrateFreshSeedOnce
{
    /**
     * If true, setup has run at least once.
     * @var boolean
     */
    protected static $setUpHasRunOnce = false;
    /**
     * If true, setup has run at least once.
     * @var boolean
     */
    protected static $classSetUpHasRun = false;

    /**
     * After the first run of setUp "migrate:fresh --seed"
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        if (!static::$setUpHasRunOnce) {
            Artisan::call('migrate:fresh');
            Artisan::call(
                'db:seed',
                ['--class' => 'DatabaseSeeder']
            );
            $this->createDummyTutor();
            $this->createDummyParent();
            $this->createDummyStudent();
            static::$setUpHasRunOnce = true;
        }
    }

    public function createDummyTutor()
    {
        $user = User::factory()->create();
        $user->assignRole('tutor');
        Tutor::factory()->create(['user_id' => $user->id]);
    }

    public function createDummyStudent()
    {
        $user = User::factory()->create(['password' => Hash::make('@Student1234')]);
        $user->assignRole('student');
        Student::factory()->create(['user_id' => $user->id]);
    }

    public function createDummyParent()
    {
        $user = User::factory()->create();
        $user->assignRole('parent');
        Parents::factory()->create(['user_id' => $user->id]);
    }
}
