<?php

namespace App\Contracts;

interface StudentRepositoryContract
{
	public function create($validatedData);
	
	public function getByEmail($email);
}
