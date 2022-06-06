<?php

namespace App\Http\Controllers\Api\User;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAgendaRequest;
use App\Http\Resources\AgendaResource;
use App\Services\AgendaService;

class AgendaController extends Controller
{
    protected $agendaService;

    public function __construct(AgendaService $agendaService)
    {
        $this->agendaService = $agendaService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $agendas = AgendaResource::collection($this->agendaService->getAll());

        if ($agendas->count() == 0) {
            throw new ModelGetEmptyException("Agenda");
        }

        return response()->json([
            'status' => 200,
            'message' => 'Agenda retrieved successfully',
            'data' => $agendas
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAgendaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAgendaRequest $request)
    {
        $validated = $request->validated();

        $agenda = $this->agendaService->create($validated);

        return response()->json([
            'status' => 200,
            'message' => 'Agenda created successfully',
            'data' => new AgendaResource($agenda)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $agendaId
     * @return \Illuminate\Http\Response
     */
    public function destroy($agendaId)
    {
        $this->agendaService->delete($agendaId);

        return response()->json([
            'status' => 200,
            'message' => 'Agenda deleted successfully'
        ], 200);
    }

    /**
     * Get Calendar
     * 
     * @return \Illuminate\Http\Response
     */
    public function calendar()
    {
        $calendar = $this->agendaService->getCalendar();

        return response()->json([
            'status' => 200,
            'message' => 'Calendar retrieved successfully',
            'data' => $calendar
        ], 200);
    }
}
