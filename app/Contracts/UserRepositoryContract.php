<?php

namespace App\Contracts;

interface UserRepositoryContract
{
	public function create($validatedData);
}
