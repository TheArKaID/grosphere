<?php

namespace App\Services;

use App\Exceptions\AgoraException;
use App\Models\Classes;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ClassService
{
    private $class;

    public function __construct(Classes $class)
    {
        $this->class = $class;
    }

    /**
     * Get By ID
     * 
     * @param int $id
     * 
     * @return Classes
     */
    public function getClassById(int $id)
    {
        return $this->class->findOrFail($id);
    }

    /**
     * Create Class
     * 
     * @param array $data
     * 
     * @return Class
     */
    public function createClass(array $data)
    {
        $class = $this->class->create($data);

        if (isset($data['thumbnail'])) {
            $fileName = $class->id . '-' . time() . '.png';
            Storage::cloud()->putFileAs('class/thumbnail', $data['thumbnail'], $fileName);
            $class->thumbnail = $fileName;
            $class->save();
        }

        return $class;
    }

    /**
     * Update Class
     * 
     * @param int $id
     * @param array $data
     * 
     * @return Classes
     */
    public function updateClass(int $id, array $data)
    {
        $class = $this->getClassById($id);

        $class->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'start_time' => $data['start_time'],
            'duration' => $data['duration']
        ]);

        if (isset($data['thumbnail'])) {
            if (Storage::cloud()->exists('class/thumbnail/' . $class->thumbnail)) {
                Storage::cloud()->delete('class/thumbnail/' . $class->thumbnail);
            }
            $fileName = $class->id . '-' . time() . '.png';
            Storage::cloud()->putFileAs('class/thumbnail', $data['thumbnail'], $fileName);
            $class->thumbnail = $fileName;
            $class->save();
        }

        return $class;
    }

    /**
     * Delete Class
     * 
     * @param int $id
     * 
     * @return bool
     */
    public function deleteClass(int $id)
    {
        $class = $this->getClassById($id);

        if (Storage::cloud()->exists('class/thumbnail/' . $class->thumbnail)) {
            Storage::cloud()->delete('class/thumbnail/' . $class->thumbnail);
        }

        return $class->delete();
    }

    /**
     * Send POST request to Agora to create meeting room
     * 
     * @param string $roomName
     * 
     * @return mixed
     */
    public function createMeetingRoom(string $roomName)
    {
        $backendUrl = config('agora.backend_url');
        $isPstnEnabled = config('agora.enable_pstn');

        $data = json_encode([
            'operationName' => 'CreateChannel',
            'variables' => [
                'title' => $roomName,
                'backendURL' => $backendUrl,
                'enablePSTN' => $isPstnEnabled,
            ],
            'query' => 'mutation CreateChannel($title: String!, $backendURL: String!, $enablePSTN: Boolean) { createChannel(title: $title, backendURL: $backendURL, enablePSTN: $enablePSTN) { passphrase { host view __typename } channel title pstn { number dtmf __typename } __typename }}',
        ]);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->withBody(($data), 'application/json')->post($backendUrl . '/query')->json();

        if(isset($response['errors'])) {
            throw new AgoraException(json_encode($response['errors']), 400);
        }
        
        return $response['data']['createChannel'];
    }
}
