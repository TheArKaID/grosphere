<?php

namespace App\Http\Controllers\Api\Super;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAgencyRequest;
use App\Http\Requests\UpdateAgencyRequest;
use App\Http\Resources\AgencyResource;
use App\Models\Agency;
use App\Services\AgencyService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class AgencyController extends Controller
{
    protected $agencyService;

    public function __construct(AgencyService $agencyService)
    {
        $this->agencyService = $agencyService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $agencies = AgencyResource::collection($this->agencyService->getAll());

        if (count($agencies) == 0) {
            throw new ModelNotFoundException('No agencies found');
        }

        return response()->json([
            'status' => 200,
            'message' => 'All agencies',
            'data' => $agencies->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAgencyRequest $request)
    {
        $agency = $this->agencyService->create($request->validated());

        return response()->json([
            'status' => 201,
            'message' => 'Agency created',
            'data' => new AgencyResource($agency)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Agency $agency)
    {
        return response()->json([
            'status' => 200,
            'message' => 'Agency',
            'data' => new AgencyResource($agency)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAgencyRequest $request, Agency $agency)
    {
        $agency = $this->agencyService->update($agency->id, $request->validated());

        return response()->json([
            'status' => 200,
            'message' => 'Agency updated',
            'data' => new AgencyResource($agency)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agency $agency)
    {
        $agency->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Agency deleted'
        ], 200);
    }

    /**
     * Create Admin User
     * 
     * @param Request $request
     * @param Agency $agency
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function createAdmin(Request $request, Agency $agency)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', 'string', Password::min(8)->letters()->numbers()->mixedCase()]
        ], [
            'password.confirmed' => 'Password confirmation does not match',
            'password.min' => 'Password must be at least 8 characters',
            'password.letters' => 'Password must contain at least one letter',
            'password.numbers' => 'Password must contain at least one number',
            'password.mixed_case' => 'Password must contain at least one uppercase and one lowercase letter',
        ]);

        $adminUser = $this->agencyService->createAdmin($agency, $data);

        return response()->json([
            'status' => 201,
            'message' => 'Admin user created',
            'data' => $adminUser
        ], 201);
    }
}