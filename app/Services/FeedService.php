<?php

namespace App\Services;

use App\Models\Feed;
use Illuminate\Support\Facades\Storage;

class FeedService
{
    function __construct(
        protected \App\Models\Feed $feed
    ) {
        $this->feed = $feed;
    }

    public function get()
    {
        return $this->feed->orderByDesc('created_at')->with(['images', 'user'])->paginate(10);
    }

    public function find($id): Feed
    {
        return $this->feed->with(['images', 'user'])->findOrFail($id);
    }

    public function create(array $data): Feed
    {
        $feed = $this->feed->create([
            'user_id' => auth()->user()->id,
            'privacy' => $data['privacy'],
            'content' => $data['content'],
        ]);

        if ($images = $data['images'] ?? false) {
            $this->uploadImages($images, $feed);
        }

        return $feed;
    }

    function uploadImages(array $images, Feed $feed): void
    {
        $feed->images()->createMany(
            collect($images)->map(function ($image) use ($feed) {
                $filePath = $image->store('feeds/' . $feed->id, 's3');
                return [
                    'url' => Storage::disk('s3')->url($filePath),
                    'content_type' => $image->getMimeType(),
                    'file_path' => $filePath,
                    'file_name' => $image->getClientOriginalName(),
                    'file_extension' => $image->getClientOriginalExtension(),
                    'file_size' => $image->getSize(),
                ];
            })->toArray()
        );
    }

    public function update(string $id, array $data): Feed
    {
        $feed = $this->find($id);

        if ($feed->user_id !== auth()->user()->id) {
            throw new \Exception('You are not authorized to delete this feed');
        }

        $feed->update($data);

        return $feed;
    }

    public function delete($id): void
    {
        $feed = $this->find($id);
        if ($feed->user_id !== auth()->user()->id) {
            throw new \Exception('You are not authorized to delete this feed');
        }
        $feed->destroy($id);
    }
}