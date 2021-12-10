<?php

namespace App\Services;

use App\Http\Resources\ParentCollection;
use App\Models\Parents;
use Illuminate\Support\Facades\DB;

class ParentService
{
    /**
     * Get All Parents
     * 
     * @return ParentCollection
     */
    public function getAll()
    {
        $parents = new Parents;
        if (request()->has('page') && request()->get('page') == 'all') {
            if (request()->has('search')) {
                $parents = $parents->whereHas('user', function ($query) {
                    $query->where('name', 'like', '%' . request()->get('search') . '%')
                        ->orWhere('email', 'like', '%' . request()->get('search') . '%')
                        ->orWhere('phone', 'like', '%' . request()->get('search') . '%');
                });
            }
            return new ParentCollection($parents->get());
        }
        return new ParentCollection($parents->paginate(request('size', 10)));
    }
}
