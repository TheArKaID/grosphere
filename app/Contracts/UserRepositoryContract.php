<?php

namespace App\Contracts;

interface UserRepositoryContract
{
	public function getAll();
	
	public function getAllWithPagination();

	public function create($validatedData);

	public function getByEmail($email);
}
