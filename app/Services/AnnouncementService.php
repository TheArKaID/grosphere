<?php

namespace App\Services;

use App\Models\Announcement;

class AnnouncementService
{
    public function __construct(
        protected Announcement $model
    ) { }

    /**
     * Get all Announcements
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll($status = 'all')
    {
        $model = $this->model;

        if ($status !== 'all') {
            $model = $model->where('status', $status);
        }
        if ($search = request()->get('search')) {
            $model = $this->search($search);
        }
        if (request()->has('page') && request()->get('page') == 'all') {
            return $model->get();
        }
        return $model->with(['admin'])->paginate(request('size', 10));
    }

    /**
     * Get One Announcement
     * 
     * @param string $id
     * 
     * @return Announcement
     */
    public function getOne($id)
    {
        return $this->model->with(['admin'])->findOrFail($id);
    }

    /**
     * Create Announcement
     * 
     * @param array $data
     * 
     * @return \App\Models\Announcement
     */
    public function create(array $data)
    {
        $admin = auth()->user();
        $data['agency_id'] = $admin->agency_id;
        $data['admin_id'] = $admin->detail->id;
        return $this->model->create($data);
    }

    /**
     * Update Announcement
     * 
     * @param string $id
     * @param array $data
     * 
     * @return Announcement
     */
    public function update($id, $data)
    {
        $model = $this->getOne($id);

        $model->update($data);

        return $model->refresh();
    }

    /**
     * Delete Announcement
     * 
     * @param string $id
     * 
     * @return \App\Models\Announcement
     */
    public function delete($id)
    {
        return $this->getOne($id)->delete();
    }

    /**
     * Search in Announcement
     * 
     * @param string $search
     * @return Announcement
     */
    public function search($search)
    {
        return $this->model
            ->where('title', 'like', '%' . $search . '%')
            ->orWhere('content', 'like', '%' . $search . '%');
    }

    /**
     * Toggle Announcement status
     * 
     * @param string $id
     * 
     * @return Announcement
     */
    public function toggleStatus($id)
    {
        $model = $this->getOne($id);

        $model->status = !$model->status;

        $model->save();

        return $model;
    }
}
