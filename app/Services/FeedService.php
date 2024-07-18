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

    public function get(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->feed->all();
    }

    public function find($id): Feed
    {
        return $this->feed->findOrFail($id);
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

    public function update(array $data): Feed
    {
        $this->feed->update($data);

        return $this->feed;
    }

    public function delete($id): void
    {
        $this->feed->destroy($id);
    }
}