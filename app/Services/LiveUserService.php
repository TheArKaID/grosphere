<?php

namespace App\Services;

use App\Models\LiveUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
     * Get Live User by token
     * 
     * @param string $token
     * 
     * @return LiveUser
     */
    public function getLiveUserByToken(string $token)
    {
        return $this->liveUser->where('token', $token)->with(['liveClass' => function ($query) {
            $query->withoutGlobalScope('agency');
        }])->firstOrFail();
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
        $data['token'] = Str::random(10);
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

    /**
     * Invalidate Live User Token
     * 
     * @param int $id
     * 
     * @return bool
     */
    public function invalidateLiveUserToken(int $id)
    {
        $liveUser = $this->liveUser->find($id);

        if (!$liveUser) {
            return false;
        }

        return $liveUser->update([
            'token' => null
        ]);
    }

    /**
     * Upload File from Agora
     * 
     * @param array $data
     * 
     * @return array|bool
     */
    public function uploadFileFromAgora(array $data)
    {
        $file = $data['file'];
        $imageName = $file->getClientOriginalName();
        $slug = Str::slug(pathinfo($imageName, PATHINFO_FILENAME));
        $extension = $file->getClientOriginalExtension();
        $res = Storage::cloud()->putFileAs('agora/presentation/' . $data['id'], $file, $slug . '.' . $extension);

        if ($res) {
            return [
                'file' => Storage::cloud()->url('agora/presentation/' . $data['id'] . '/' . $slug . '.' . $extension),
                'shown_filename' => $slug . '.' . $extension,
                'ext' => $extension,
                'size' => round($file->getSize() / 1024, 0),
                'updated_at' => Carbon::now()->formatLocalized('%d %B %Y')
            ];
        }

        return false;
    }

    /**
     * Get File from Agora. Get All files from a directory
     * 
     * @param int $id
     * 
     * @return array|bool
     */
    public function getFileFromAgora(int $id)
    {
        $files = Storage::cloud()->files('agora/presentation/' . $id);

        if ($files) {
            $files = array_map(function ($file) {
                return [
                    'file' => Storage::cloud()->url($file),
                    'shown_filename' => pathinfo($file, PATHINFO_FILENAME) . '.' . pathinfo($file, PATHINFO_EXTENSION),
                    'ext' => pathinfo($file, PATHINFO_EXTENSION),
                    'size' => round(Storage::cloud()->size($file) / 1024, 0),
                    'updated_at' => Carbon::now()->formatLocalized('%d %B %Y')
                ];
            }, $files);
            return $files;
        }
        return false;
    }
    
    /**
     * Delete File from Agora
     * 
     * @param int $id
     * @param string $filename
     * 
     * @return bool
     */
    public function deleteFileFromAgora(int $id, string $filename)
    {
        $file = 'agora/presentation/' . $id . '/' . $filename;
        return Storage::cloud()->delete($file);
    }
}
