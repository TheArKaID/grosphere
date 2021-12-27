<?php

namespace App\Services;

use App\Models\LiveUser;
use Illuminate\Support\Carbon;

class LiveUserService
{
    private LiveUser $liveUser;

    public function __construct(LiveUser $liveUser)
    {
        $this->liveUser = $liveUser;
    }

    /**
     * Get Live User 
     * 
     * @param int $id
     * 
     * @return LiveUser
     */
    public function getLiveUserById(int $id)
    {
        return $this->liveUser->findOrFail($id);
    }

    /**
     * Get Live User by id and user id
     * 
     * @param int $liveClassid
     * @param int $userId
     * 
     * @return LiveUser
     */
    public function getLiveUserByLiveClassIdAndUserId(int $liveClassid, int $userId)
    {
        return $this->liveUser->where('live_class_id', $liveClassid)->where('user_id', $userId)->firstOrFail();
    }

    /**
     * Join or Rejoin Live User status
     * 
     * @param array $data
     * 
     * @return LiveUser
     */
    public function joinOrRejoinLiveUser(array $data)
    {
        $data['status'] = LiveUser::$STATUS_IN;
        $data['time_in'] = Carbon::now();
        $liveUser = $this->liveUser->updateOrCreate([
            'user_id' => $data['user_id'],
            'live_class_id' => $data['live_class_id']
        ], $data);

        if (array_key_first($liveUser->getAttributes()) == 'id') {
            $liveUser->increment('entries', 1);
        }
        return $liveUser;
    }

    /**
     * Leave Live User status
     * 
     * @param array $data
     * 
     * @return bool
     */
    public function leaveLiveUser(array $data)
    {
        $liveUser = $this->getLiveUserByLiveClassIdAndUserId($data['live_class_id'], $data['user_id']);

        return $liveUser->update([
            'time_out' => Carbon::now(),
            'status' => LiveUser::$STATUS_OUT
        ]);
    }
}
