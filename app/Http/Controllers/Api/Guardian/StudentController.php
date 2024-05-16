<?php

namespace App\Http\Controllers\Api\Guardian;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Http\Resources\SubscriptionResource;
use App\Models\Student;
use App\Services\PaymentSubscriptionService;
use App\Services\StudentService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    protected $studentService, $subscriptionsService;

    public function __construct(StudentService $studentService, PaymentSubscriptionService $subscriptionsService)
    {
        $this->studentService = $studentService;
        $this->subscriptionsService = $subscriptionsService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = StudentResource::collection($this->studentService->getByGuardian(auth()->user()->detail->id));

        if (!$students->count()) {
            throw new ModelGetEmptyException("Student's Guardian");
        }

        return response()->json([
            'status' => 200,
            'message' => 'All student`s Guardian',
            'data' => $students->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        return response()->json([
            'status' => 200,
            'message' => 'Student detail',
            'data' => new StudentResource($student->with(['user', 'courseStudents.studentClasses', 'subscriptions'])->first())
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        //
    }

    /**
     * Get All Subscription of all Students
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscriptions()
    {
        $subscriptions = SubscriptionResource::collection($this->subscriptionsService->getByGuardian(auth()->user()->detail->id)->load(['student', 'invoices']));

        if (!$subscriptions->count()) {
            throw new ModelGetEmptyException("Subscription's Guardian");
        }

        return response()->json([
            'status' => 200,
            'message' => 'All Subscription of all Students',
            'data' => $subscriptions
        ], 200);
    }
}
