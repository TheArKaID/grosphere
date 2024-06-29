<?php

namespace App\Services;

use App\Models\Agency;
use App\Models\Student;

class FrontService
{
	/**
	 * Search some users of student by email
	 * 
	 * @param string $email
	 * 
	 * @return Student
	 */
	public function searchByEmail(string $email)
	{
		return Student::whereHas('user', function ($query) use ($email) {
			$query->where('email', '=', $email);
		})->get();
	}

    /**
     * Get Theme of Agency
     * 
     * @return array
     */
    public function getTheme(string $subdomain)
    {
		$subdomain = explode('.', $subdomain)[0];
		return $subdomain !== 'postman' ? Agency::where('website', $subdomain)->first() : Agency::first();
	}
}
