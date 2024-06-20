<?php

namespace App\Services;

use App\Models\Chapter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ChapterService
{
    private $chapter;

    public function __construct(Chapter $chapter)
    {
        $this->chapter = $chapter;
    }

    /**
     * Get all Chapters
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(int $curriculum_id)
    {
        if (request()->has('search')) {
            $search = request()->get('search');
            $this->chapter = $this->search($search);
        }
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->chapter->get();
        }
        return $this->chapter->where('curriculum_id', $curriculum_id)->paginate(request('size', 10));
    }

    /**
     * Get One Chapter
     * 
     * @param string $id
     * 
     * @return Chapter
     */
    public function getOne($id)
    {
        return $this->chapter->findOrFail($id);
    }

    /**
     * Create Chapter
     * 
     * @param string $curriculum_id
     * @param array $data
     * 
     * @return \App\Models\Chapter
     */
    public function create(string $curriculum_id, array $data)
    {
        $data['curriculum_id'] = $curriculum_id;
        
        if (!isset($data['content_type'])) {
            $data['content_type'] = $this->getContentType($data['content']);
        }

        if(request()->hasFile('content')) {
            $data['file_path'] = request()->file('content')->store('primary/' . $curriculum_id, 's3');
            $data['content'] = Storage::disk('s3')->url($data['file_path']);
            $data['file_name'] = request()->file('content')->getClientOriginalName();
            $data['file_extension'] = request()->file('content')->getClientOriginalExtension();
            $data['file_size'] = request()->file('content')->getSize();
        }

        return $this->chapter->create($data);
    }

    /**
     * Update Chapter
     * 
     * @param Chapter $chapter
     * @param array $data
     * 
     * @return Chapter
     */
    public function update(Chapter $chapter, $data)
    {
        if(request()->hasFile('content')) {
            if (!isset($data['content_type'])) {
                $data['content_type'] = $this->getContentType($data['content']);
            }
    
            Storage::disk('s3')->delete($chapter->file_path);
            $data['file_path'] = request()->file('content')->store('primary/' . $chapter->curriculum_id, 's3');
            $data['content'] = Storage::disk('s3')->url($data['file_path']);
            $data['file_name'] = request()->file('content')->getClientOriginalName();
            $data['file_extension'] = request()->file('content')->getClientOriginalExtension();
            $data['file_size'] = request()->file('content')->getSize();
        }

        $chapter->update($data);
        return $chapter;
    }

    /**
     * Delete Chapter
     * 
     * @param Chapter $chapter
     * 
     * @return \App\Models\Chapter
     */
    public function delete(Chapter $chapter)
    {
        // Delete file
        if ($chapter->file_path)
            Storage::disk('s3')->delete($chapter->file_path);
        $chapter->delete();
        return $chapter;
    }

    /**
     * Search in Chapter
     * 
     * @param string $search
     * @return Chapter
     */
    public function search($search)
    {
        return $this->chapter->where('name', 'like', '%' . $search . '%')
        ->orWhere('description', 'like', '%' . $search . '%')
        ->orWhere('content', 'like', '%' . $search . '%');
    }

    /**
     * Content could be Text, or PDF, Video or Image uploaded file. Get the MIME content type of the content.
     * 
     * @param string|\Illuminate\Http\UploadedFile $content
     * 
     * @return string
     */
    public function getContentType($content)
    {
        if (is_string($content)) {
            return 'text/plain';
        }

        if ($content->isValid()) {
            return $content->getMimeType();
        }

        return 'application/octet-stream';
    }
}
