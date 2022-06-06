<?php

namespace App\Services;

use App\Models\Agenda;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AgendaService
{
    private $agenda;

    public function __construct(Agenda $agenda)
    {
        $this->agenda = $agenda;
    }

    /**
     * Get all Agendas
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        if (request()->has('date')) {
            $date = request()->get('date');
            $this->agenda = $this->dateFilter($date);
        }

        return $this->agenda->where('user_id', Auth::user()->id)->get();
    }

    /**
     * Date FIlter
     * 
     * @param string $date
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function dateFilter($date)
    {
        return $this->agenda->whereDate('date', Carbon::parse($date)->format('Y-m-d'));
    }

    /**
     * Create Agenda
     * 
     * @param array $data
     * 
     * @return \App\Models\Agenda
     */
    public function create($data)
    {
        $agenda = new Agenda();
        $agenda->user_id = Auth::user()->id;
        $agenda->date = $data['date'];
        $agenda->detail = $data['detail'];
        $agenda->save();

        return $agenda;
    }

    /**
     * Delete Agenda
     * 
     * @param int $agendaId
     * 
     * @return \App\Models\Agenda
     */
    public function delete($agendaId)
    {
        $agenda = $this->agenda->where('user_id', Auth::user()->id)->findOrFail($agendaId);
        $agenda->delete();

        return $agenda;
    }
}
