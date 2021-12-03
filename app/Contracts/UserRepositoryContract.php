<?php

namespace App\Contracts;

interface UserRepositoryContract
{
	public function create($validatedData);

	public function getByEmail($email);
}
