<?php

namespace App\Services;

use App\Models\Agency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AgencyService
{
    private $agency;

    public function __construct(Agency $agency)
    {
        $this->agency = $agency;
    }

    /**
     * Get all Agencys
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        if ($search = request()->get('search')) {
            $this->agency = $this->search($search);
        }
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->agency->get();
        }
        return $this->agency->paginate(request('size', 10));
    }

    /**
     * Get One Agency
     * 
     * @param int $id
     * 
     * @return Agency
     */
    public function getOne($id)
    {
        return $this->agency->findOrFail($id);
    }

    /**
     * Create Agency
     * 
     * @param array $data
     * 
     * @return \App\Models\Agency
     */
    public function create($data)
    {
        DB::beginTransaction();

        $agency = $this->agency->create($data);

        $data['logo'] = base64_decode(substr($data['logo'], strpos($data['logo'], ",")+1));
        Storage::disk('s3')->put('agencies/' . $agency->id . '.png', $data['logo']);

        DB::commit();
        return $agency;
    }

    /**
     * Update Agency
     * 
     * @param int $id
     * @param array $data
     * 
     * @return Agency
     */
    public function update($id, $data)
    {
        DB::beginTransaction();

        $agency = $this->getOne($id);
        $agency->update($data);

        if ($data['logo'] ?? false) {
            $data['logo'] = base64_decode(substr($data['logo'], strpos($data['logo'], ",")+1));
            Storage::disk('s3')->put('agencies/' . $agency->id . '.png', $data['logo']);
        }

        DB::commit();
        return $agency;
    }

    /**
     * Delete Agency
     * 
     * @param int $id
     * 
     * @return \App\Models\Agency
     */
    public function delete($id)
    {
        DB::beginTransaction();
        $agency = $this->getOne($id);
        $agency->delete();

        DB::commit();
        return true;
    }

    /**
     * Search in Agency
     * 
     * @param string $search
     * @return Agency
     */
    public function search($search)
    {
        return $this->agency
            ->whereHas('user', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
    }
}
