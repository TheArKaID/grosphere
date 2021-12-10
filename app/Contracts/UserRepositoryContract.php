<?php

namespace App\Contracts;

interface UserRepositoryContract
{
	public function getAll();
	
	public function getAllWithPagination();

	public function getByEmail($email);

	public function create($data);

	public function update($id, $data);
}
