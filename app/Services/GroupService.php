<?php

namespace App\Services;

use App\Models\Group;
use App\Models\GroupStudent;
use Illuminate\Support\Facades\DB;

class GroupService
{
    private $group, $groupStudent;

    public function __construct(Group $group, GroupStudent $groupStudent)
    {
        $this->group = $group;
        $this->groupStudent = $groupStudent;
    }

    /**
     * Get all Groups
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        if (request()->has('search')) {
            $search = request()->get('search');
            $this->group = $this->search($search);
        }
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->group->get();
        }
        return $this->group->paginate(request('size', 10));
    }

    /**
     * Get One Group
     * 
     * @param int $id
     * 
     * @return Group
     */
    public function getOne($id)
    {
        return $this->group->findOrFail($id);
    }

    /**
     * Create Group
     * 
     * @param array $data
     * 
     * @return \App\Models\Group
     */
    public function create($data)
    {
        $group = $this->group->create($data);

        return $group;
    }

    /**
     * Update Group
     * 
     * @param int $id
     * @param array $data
     * 
     * @return Group
     */
    public function update($id, $data)
    {
        $group = $this->group->findOrFail($id);
        $group->update($data);

        return $group;
    }

    /**
     * Delete Group
     * 
     * @param int $id
     * 
     * @return \App\Models\Group
     */
    public function delete($id)
    {
        DB::beginTransaction();

        $group = $this->getOne($id);
        $group->delete();

        DB::commit();
        return true;
    }

    /**
     * Search in Group
     * 
     * @param string $search
     * @return mixed
     */
    public function search($search)
    {
        return $this->group->where('name', 'like', '%' . $search . '%');
    }

    /**
     * Add Student to Group by Creating Group Student
     * 
     * @param int $groupId
     * @param array $studentIds
     * 
     * @return \App\Models\Group
     */
    public function addStudent($groupId, $studentIds)
    {
        $group = $this->getOne($groupId);
        $group->students()->attach($studentIds);

        return $group;
    }

    /**
     * Remove Student from Group by Deleting Group Student
     * 
     * @param int $groupId
     * @param array $studentIds
     * 
     * @return \App\Models\Group
     */
    public function removeStudent($groupId, $studentIds)
    {
        $group = $this->getOne($groupId);
        $group->students()->detach($studentIds);

        return $group;
    }
}
