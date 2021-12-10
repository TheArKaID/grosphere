<?php

namespace App\Contracts;

interface StudentRepositoryContract
{
	public function getAll();

	public function getAllWithPagination($perPage = 10);

	public function create($validatedData);
	
	public function getByEmail($email);
}
