<?php

namespace App\Services;

use App\Models\Feed;
use Illuminate\Database\Eloquent\Builder;
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
        if (auth()->user()->roles->pluck('name')->toArray()[0] !== 'admin') {
            return $this->feed->orderByDesc('created_at')->with(['images', 'user'])->paginate(10);
        }

        $feeds = $this->feed->where(function (Builder $query) {
            $query->where('privacy', 'all')->orWhere('user_id', auth()->user()->id);
        });

        if(auth()->user()->roles->pluck('name')->toArray()[0] == 'teacher') {
            $feeds = $feeds->orWhere(function (Builder $query) {
                $query->where('privacy', 'group')
                // Teacher could see students' feeds in the same class group
                ->whereHas('user.student.classGroups.teachers', function (Builder $query) {
                    $query->where('user_id', auth()->user()->id);
                })
                // Or their guardian's feeds
                ->orWhereHas('user.guardian.students.classGroups.teachers', function (Builder $query) {
                    $query->where('user_id', auth()->user()->id);
                });
            });
        } else if(auth()->user()->roles->pluck('name')->toArray()[0] == 'student') {
            $feeds = $feeds->orWhere(function (Builder $query) {
                $query->where('privacy', 'group')
                // Student could see their class group's feeds
                ->whereHas('user.student.classGroups.students', function (Builder $query) {
                    $query->where('user_id', auth()->user()->id);
                })
                // Or their guardian's feeds
                ->orWhereHas('user.guardian.students.classGroups.students', function (Builder $query) {
                    $query->where('user_id', auth()->user()->id);
                })
                // Or their teacher's feeds
                ->orWhereHas('user.teacher.classGroups.students', function (Builder $query) {
                    $query->where('user_id', auth()->user()->id);
                });
            });
        } else if (auth()->user()->roles->pluck('name')->toArray()[0] == 'guardian') {
            $feeds = $feeds->orWhere(function (Builder $query) {
                $query->where('privacy', 'group')
                // Guardian could see their children' feeds
                ->whereHas('user.student.guardians', function (Builder $query) {
                    $query->where('user_id', auth()->user()->id);
                })
                // Or their student's friends' feeds
                ->orWhereHas('user.student.classGroups.students.guardians', function (Builder $query) {
                    $query->where('user_id', auth()->user()->id);
                })
                // Or their student's friends' guardian's feeds
                ->orWhereHas('user.guardian.students.classGroups.students.guardians', function (Builder $query) {
                    $query->where('user_id', auth()->user()->id);
                })
                // Or their student's teacher's feeds
                ->orWhereHas('user.teacher.classGroups.students.guardians', function (Builder $query) {
                    $query->where('user_id', auth()->user()->id);
                });
            });
        }

        return $feeds->orderByDesc('created_at')->with(['images', 'user'])->paginate(10);
    }

    public function find($id): Feed
    {
        return $this->feed->with(['images', 'user', 'comments.user', 'likes'])->findOrFail($id);
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

    public function comment(string $id, string $content): void
    {
        $feed = $this->find($id);
        $feed->comments()->create([
            'user_id' => auth()->user()->id,
            'content' => $content
        ]);
    }

    public function likeUnlike(string $id): void
    {
        $feed = $this->find($id);
        $feed->likes()->toggle(auth()->user()->id);
    }
}