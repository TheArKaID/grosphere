<?php

namespace App\Services;

use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use Doctrine\DBAL\Query;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    protected $attendance;

    public function __construct(Attendance $attendance)
    {
        $this->attendance = $attendance;
    }

    /**
     * Get All Student attendance pair, in and out.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function all() : \Illuminate\Database\Eloquent\Collection
    {
        return $this->attendance->with('student')->get();
    }

    /**
     * Pair all attendances in and out for everyday.
     * 
     * @param string $parent_id
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function pair($parent_id = null)
    {
        if ($parent_id) {
            $this->attendance = $this->attendance->where('guardian_id', $parent_id);
        }

        if ($search = request()->get('search', false)) {
            $this->attendance = $this->attendance
            ->where(function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->whereHas('student', function ($query) use ($search) {
                        $query->whereHas('user', function ($query) use ($search) {
                            $query->where('name', 'like', "%$search%");
                        });
                    });
                })
                ->orWhere(function ($query)  use ($search) {
                    $query->whereHas('admin', function ($query) use ($search) {
                        $query->whereHas('user', function ($query) use ($search) {
                            $query->where('name', 'like', "%$search%");
                        });
                    });
                });
            });
        }
        if($date = request()->get('date', false)) {
            $this->attendance = $this->attendance->whereDate('created_at', $date);
        }

        $this->attendance = $this->attendance->with(['student.user', 'guardian.user'])
        ->whereHas('student.user', function ($query) {
            $query->where('agency_id', auth()->user()->agency_id);
        })
        ->whereType('in')->orderBy('id', 'desc');
        return request()->has('page') && request()->get('page') == 'all' ? $this->attendance->get() : $this->attendance->paginate(request('size', 10));
    }

    /**
     * Get all 'in' attendance records.
     * 
     * @param string $filter
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function totalIn($filter = null) : \Illuminate\Database\Eloquent\Collection
    {
        $this->attendance = $this->attendance->select(['id', 'student_id', 'type', 'created_at'])->whereType('in');

        if ($filter) {
            $this->attendance = $this->filterByRange($filter);
        }

        return $this->attendance->get();
    }

    /**
     * Filter weekly or monthly attendance records.
     * 
     * @param string $filter
     * 
     * @return Builder|EloquentBuilder
     */
    function filterByRange(string $filter) : Builder|EloquentBuilder
    {
        switch ($filter) {
            case 'weekly':
                return $this->attendance->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            default:
                return $this->attendance->whereMonth('created_at', now()->month);
                break;
        }
    }
    /**
     * Create a new attendance record.
     * 
     * @param array $data
     * 
     * @return Attendance
     */
    function create(array $data) : Attendance
    {
        $validated = $this->validate($data);

        if ($validated !== true) {
            throw ValidationException::withMessages($validated);
        }
        DB::beginTransaction();

        $proof = $data['proof'];
        $fileName = 'attendances/' . $data['student_id'] . '_' . now()->format('Y-m-d') . '_' . $data['type'] . '.' . explode('/', explode(':', substr($proof, 0, strpos($proof, ';')))[1])[1];

        $data['proof'] = '';
        $data['admin_id'] = auth()->user()->id;
        $attendance = $this->attendance->create($data);

        // Proof is image base64 encoded
        // Decode to image and store to s3
        $data['proof'] = base64_decode(substr($proof, strpos($proof, ",")+1));
        Storage::disk('s3')->put($fileName, $data['proof']);
        $data['proof'] = $fileName;
        $attendance->proof = $data['proof'];
        $attendance->save();
        DB::commit();
        return $attendance;
    }

    /**
     * Validate the attendance of student.
     * Student should check in before check out.
     * After the checkout, student could check in again.
     * 
     * @param array $data
     * 
     * @return array|bool
     */
    function validate(array $data) : array|bool
    {
        $guardianService = app(GuardianService::class);
        if (!$guardianService->isGuardian($data['guardian_id'], $data['student_id'])) {
            return ['guardian' => 'Guardian is not the guardian of the student'];
        }

        $attendance = $this->attendance->where('student_id', $data['student_id'])
            ->whereDate('created_at', now()->toDateString())
            ->latest()
            ->first();

        if ($attendance) {
            if ($attendance->type === 'in' && $data['type'] === 'in') {
                return ['check-type' => 'Student already checked in'];
            }

            if ($attendance->type === 'out' && $data['type'] === 'out') {
                return ['check-type' => 'Student already checked out'];
            }
        } elseif ($data['type'] === 'out') {
            return ['check-type' => 'Student has not checked in'];
        }

        return true;
    }

    /**
     * Get the attendance record by in id.
     * 
     * @param integer $id
     * @param integer $guardian_id
     * 
     * @return array
     */
    function find(int $id, int $guardian_id = null) : array
    {
        $in = $this->attendance->where('type', 'in')->where(function ($query) use ($guardian_id) {
            if ($guardian_id) {
                $query->where('guardian_id', $guardian_id);
            }
        })->findOrFail($id)
        ->setHidden(['out'])->load(['student.user', 'guardian']);

        $out = $this->attendance->where('student_id', $in->student_id)
            ->where('type', 'out')
            ->whereDate('created_at', $in->created_at)
            ->first()?->setHidden(['out'])?->load(['student.user', 'guardian']);

        return [
            'in' => AttendanceResource::make($in),
            'out' => $out ? AttendanceResource::make($out) : null
        ];
    }
}
