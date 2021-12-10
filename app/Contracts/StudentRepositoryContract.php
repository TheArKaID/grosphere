<?php

namespace App\Contracts;

interface StudentRepositoryContract
{
	public function create($validatedData);
}
