<?php

namespace App\Services;

use App\Models\ClassMaterial;

class ClassMaterialService
{
    public function __construct(
        protected ClassMaterial $model
    ) { }

    /**
     * Get all ClassMaterials
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        $model = $this->model;

        if (request()->has('page') && request()->get('page') == 'all') {
            return $model->get();
        }
        return $model->with(['detail'])->paginate(request('size', 10));
    }

    /**
     * Get One ClassMaterial
     * 
     * @param string $id
     * 
     * @return ClassMaterial
     */
    public function getOne($id)
    {
        return $this->model->with(['detail'])->findOrFail($id);
    }

    /**
     * Create ClassMaterial
     * 
     * @param array $data
     * 
     * @return \App\Models\ClassMaterial
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Delete ClassMaterial
     * 
     * @param string $id
     * 
     * @return \App\Models\ClassMaterial
     */
    public function delete($id)
    {
        return $this->getOne($id)->delete();
    }
}
