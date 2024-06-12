<?php

namespace App\Services;

use App\Exceptions\TeacherFileException;
use App\Models\Teacher;
use App\Models\TeacherFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TeacherService
{
    private $teacher, $userService, $teacherFile
    // , $liveClassService
    ;

    public function __construct(
        Teacher $teacher,
        TeacherFile $teacherFile,
        UserService $userService,
        // LiveClassService $liveClassService
    ) {
        $this->teacher = $teacher;
        $this->userService = $userService;
        $this->teacherFile = $teacherFile;
        // $this->liveClassService = $liveClassService;
    }

    /**
     * Get All Teacher
     * 
     * @return Teacher
     */
    public function getAll()
    {
        if (request()->has('search')) {
            $this->teacher = $this->teacher->whereHas('user', function ($query) {
                $query->where('name', 'like', '%' . request()->get('search') . '%')
                    ->orWhere('email', 'like', '%' . request()->get('search') . '%')
                    ->orWhere('phone', 'like', '%' . request()->get('search') . '%');
            });
        }
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->teacher->get();
        }
        return $this->teacher->paginate(request('size', 10));
    }

    /**
     * Count Teachers
     * 
     * @return int
     */
    public function count()
    {
        return $this->teacher->count();
    }

    /**
     * Get Teacher By Id
     * 
     * @param int $id
     * @return Teacher
     */
    public function getById(int $id)
    {
        return $this->teacher->findOrFail($id);
    }

    /**
     * Create Teacher
     * 
     * @param array $data
     * @return Teacher
     */
    public function create(array $data)
    {
        DB::beginTransaction();

        $data['password'] = bcrypt($data['password']);

		$data['agency_id'] = auth()->user()->agency_id;

        $user = $this->userService->createUser($data);
        $data['user_id'] = $user->id;
        $teacher = $this->teacher->create($data);

        // Profile is image base64 encoded
        // Decode to image and store to s3
        $data['photo'] = base64_decode(substr($data['photo'], strpos($data['photo'], ",")+1));
        Storage::disk('s3')->put('teachers/' . $teacher->id . '.png', $data['photo']);

        $user->assignRole('teacher');

        DB::commit();

        return $teacher;
    }

    /**
     * Update Teacher
     * 
     * @param int $id
     * @param array $data
     * @return Teacher
     */
    public function update(int $id, array $data)
    {
        DB::beginTransaction();

        $teacher = $this->getById($id);

        $teacher->update($data);
		$this->userService->updateUser($teacher->user_id, $data);

        if ($photo = $data['photo'] ?? false) {
            $photo = base64_decode(substr($photo, strpos($photo, ",")+1));
            Storage::disk('s3')->put('teachers/' . $teacher->id . '.png', $photo);
        }

        DB::commit();

        return $teacher;
    }

    /**
     * Delete Teacher
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id)
    {
        DB::beginTransaction();

        $teacher = $this->getById($id);

        $teacher->delete();
        $this->userService->deleteUser($teacher->user_id);

        DB::commit();

        return true;
    }

    /**
     * Change Teacher Password
     * 
     * @param int $id
     * @param string $password
     * 
     * @return bool
     */
    public function changePassword(int $id, string $password)
    {
        $teacher = $this->getById($id);

        $this->userService->changePassword($teacher->user_id, $password);

        return true;
    }

    /**
     * Change Teacher's password By Teacher
     * 
     * @param int $id
     * @param array $data
     * 
     * @return bool
     */
    public function changePasswordByTeacher(int $id, array $data)
    {
        $teacher = $this->getById($id);

        $this->userService->changePassword($teacher->user_id, $data['new_password']);

        return true;
    }

    // /**
    //  * Teacher Join Live Class
    //  * 
    //  * @param int $liveClassId
    //  * 
    //  * @return LiveUser|string
    //  */
    // public function joinLiveClass($liveClassId)
    // {
    //     $status = $this->liveClassService->isTeacherLiveClassNotStarted($liveClassId);
    //     if (gettype($status) == 'string') {
    //         return $status;
    //     }
    //     $status = $this->liveClassService->isTeacherLiveClassNotEnded($liveClassId);
    //     if (gettype($status) == 'string') {
    //         return $status;
    //     }
    //     return $this->userService->userJoinLiveClass($liveClassId);
    // }

    /**
     * Teacher leave Live Class
     * 
     * @param int $id
     * 
     * @return bool
     */
    public function leaveLiveClass($id)
    {
        // Do What ?
        // $liveClass = $this->liveClassService->getLiveClassById($id);
        // $userId = auth()->user()->id;
        // $data = [
        //     'user_id' => $userId,
        //     'live_class_id' => $liveClass->id
        // ];

        // return $this->liveUserService->leaveLiveTeacher($data);
    }

    /**
     * Get All Teacher File
     * 
     * @param int $teacherId
     * 
     * @return TeacherFile
     */
    public function getAllTeacherFile(int $teacherId)
    {
        return $this->teacherFile->where('teacher_id', $teacherId)->get();
    }

    /**
     * Get Teacher File By Id
     * 
     * @param int $id
     * 
     * @return TeacherFile
     */
    public function getTeacherFileById(int $id)
    {
        return $this->teacherFile->findOrFail($id);
    }

    /**
     * Create Teacher File
     * 
     * @param int $teacherId
     * @param array $data
     * 
     * @return TeacherFile
     */
    public function createTeacherFile(int $teacherId, array $data)
    {
        try {
            $data['teacher_id'] = $teacherId;
    
            if ($file = request()->file('content')) {
                $data['file_size'] = $file->getSize();   
                if ($this->isReachMaxFileSize($teacherId, $data['file_size'])) {
                    throw new TeacherFileException('Teacher reach max file size. Currently using '. $this->getTotalFileSizeMb($teacherId) .' of ' . $this->getMaxFileSizeMb() . '. Please delete some files.');
                }

                if (!isset($data['content_type'])) {
                    $data['content_type'] = $file->getMimeType();
                }
        
                $data['file_path'] = $file->store('teachers/' . $teacherId, 's3');
                $data['content'] = Storage::disk('s3')->url($data['file_path']);
                $data['file_name'] = $file->getClientOriginalName();
                $data['file_extension'] = $file->getClientOriginalExtension();
            }
    
            return $this->teacherFile->create($data);
        } catch (\Exception $e) {
            if ($data['file_path']){
                Storage::disk('s3')->delete($data['file_path']);
            }
            throw new TeacherFileException('Filed to upload file. Please contact Administrator. ' . $e->getMessage());
        }
    }

    /**
     * Update Teacher File
     * 
     * @param TeacherFile $teacherFile
     * @param array $data
     * 
     * @return TeacherFile
     */
    public function updateTeacherFile(TeacherFile $teacherFile, array $data)
    {
        DB::beginTransaction();
        try {
            if ($file = request()->file('content')) {
                // Before delete, move to another storage first
                Storage::disk('s3')->move($teacherFile->file_path, 'teachers/deleted/' . $teacherFile->file_name);

                $data['file_path'] = $file->store('teachers/' . $teacherFile->teacher_id, 's3');
                if (isset($data['content_type'])) {
                    $data['content_type'] = $file->getMimeType();
                }

                $data['content'] = Storage::disk('s3')->url($data['file_path']);
                $data['file_name'] = $file->getClientOriginalName();
                $data['file_extension'] = $file->getClientOriginalExtension();
                $data['file_size'] = $file->getSize();
            }
    
            $teacherFile->update($data);
    
            Storage::delete('teachers/deleted/' . $teacherFile->file_name);
            DB::commit();
            return $teacherFile;
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($data['file_path']){
                Storage::disk('s3')->delete($data['file_path']);
                Storage::disk('s3')->move('teachers/deleted/' . $teacherFile->file_name, $teacherFile->file_path);
            }
            throw new TeacherFileException('Filed to upload file. Please contact Administrator. ' . $th->getMessage());
        }
        
    }

    /**
     * Delete Teacher File
     * 
     * @param TeacherFile $teacherFile
     * 
     * @return bool
     */
    public function deleteTeacherFile(TeacherFile $teacherFile)
    {
        Storage::delete($teacherFile->content);

        $teacherFile->delete();

        return true;
    }

    /**
     * Check if teacher reach max file size
     * 
     * @param int $teacherId
     * @param int $fileSize
     * 
     * @return bool
     */
    public function isReachMaxFileSize(int $teacherId, int $fileSize)
    {
        // 2MB is for safety
        return ($this->getTotalFileSize($teacherId) + $fileSize + 2 * 1024 *1024) > $this->getMaxFileSize();
    }

    /**
     * Get total of teacher file size
     * 
     * @param int $teacherId
     * 
     * @return int
     */
    public function getTotalFileSize(int $teacherId)
    {
        return $this->teacherFile->where('teacher_id', $teacherId)->sum('file_size');
    }

    /**
     * Get total of teacher file size
     * 
     * @param int $teacherId
     * 
     * @return int
     */
    public function getTotalFileSizeMb(int $teacherId)
    {
        return round($this->getTotalFileSize($teacherId) / 1024 / 1024, 2);
    }

    /**
     * Get maximum file size for teacher
     * 
     * @return int
     */
    public function getMaxFileSize()
    {
        return config('app.teacher.file.max_size');
    }
    
    /**
     * Get maximum file size for teacher in MB
     * 
     * @return int
     */
    public function getMaxFileSizeMb()
    {
        return round($this->getMaxFileSize() / 1024 / 1024, 2);
    }
}
