<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\AnnouncementUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AnnouncementService
{
    private $annoucement;

    public function __construct(Announcement $annoucement)
    {
        $this->annoucement = $annoucement;
    }

    /**
     * Get all announcements
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        if (request()->has('search')) {
            $search = request()->get('search');
            $this->annoucement = $this->search($search);
        }
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->annoucement->get();
        }
        return $this->annoucement->paginate(request('size', 10));
    }

    /**
     * Get all announcements for user
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllForUser()
    {
        return $this->annoucement->where(function (Builder $query) {
            $query->where('to', '=', Announcement::$ALL)
                ->orWhere('to', '=', $this->getToForUser());
        })->get();
    }

    /**
     * Get To of User for announcement
     * 
     * @return int
     */
    public function getToForUser()
    {
        return Auth::user()->hasRole('tutor') ? Announcement::$TUTOR : (Auth::user()->hasRole('student') ? Announcement::$STUDENT : Announcement::$PARENT);
    }

    /**
     * Get announcement by id
     * 
     * @param int $id
     * 
     * @return Announcement
     */
    public function getById($id)
    {
        return $this->annoucement->findOrFail($id);
    }

    /**
     * Get by ID for User
     * 
     * @param int $id
     * 
     * @return Announcement
     */
    public function getByIdForUser($id)
    {
        $annoucement = $this->annoucement->where(function ($query) {
            $query->where('to', Announcement::$ALL)->orWhere('to', $this->getToForUser());
        })->findOrFail($id);
        
        $this->createUserAnnouncement($annoucement->id);

        return $annoucement;
    }

    /**
     * Create User announcement as announcement read
     * 
     * @param int $announcementId
     *
     * @return AnnouncementUser
     */
    public function createUserAnnouncement($announcementId)
    {
        $annoucementUser = AnnouncementUser::select('id')->where('user_id', Auth::user()->id)->where('announcement_id', $announcementId)->first();

        if (!$annoucementUser) {
            return AnnouncementUser::create([
                'user_id' => Auth::user()->id,
                'announcement_id' => $announcementId,
            ]);
        }
        return $annoucementUser;
    }

    /**
     * Create announcement
     * 
     * @param array $data
     * 
     * @return Announcement
     */
    public function create(array $data)
    {
        return $this->annoucement->create($data);
    }

    /**
     * Update announcement
     * 
     * @param int $id
     * @param array $data
     * 
     * @return Announcement
     */
    public function update($id, array $data)
    {
        $announcement = $this->annoucement->findOrFail($id);
        $announcement->update($data);
        return $announcement;
    }

    /**
     * Delete announcement
     * 
     * @param int $id
     * 
     * @return bool
     */
    public function delete($id)
    {
        $announcement = $this->annoucement->findOrFail($id);
        return $announcement->delete();
    }

    /**
     * Search Announcement
     * 
     * @param string $search
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function search($search)
    {
        return $this->annoucement->where('name', 'like', '%' . $search . '%')
            ->orWhere('message', 'like', '%' . $search . '%');
    }
}
