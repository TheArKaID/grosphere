<?php

namespace App\Contracts;

interface StudentRepositoryContract
{
	public function getAll();

	public function getAllWithPagination($perPage = 10);

	public function getById($id);

	public function getByEmail($email);

	public function create($data);
	
	public function update($id, $data);

	public function delete($id);
}
