<?php

namespace App\Services;

use App\Models\Agenda;
// use App\Models\Calendar;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AgendaService
{
    private $agenda
    // , $calendars
    ;

    public function __construct(
        Agenda $agenda
        // , Calendar $calendars
    )
    {
        $this->agenda = $agenda;
        // $this->calendars = $calendars;
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
            $this->agenda = $this->dateFilter($date, $this->agenda);
        }

        return $this->agenda->where('user_id', Auth::user()->id)->get();
    }

    /**
     * Date FIlter
     * 
     * @param string $date
     * @param mix $model
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function dateFilter($date, $model)
    {
        return $model->whereDate('date', Carbon::parse($date)->format('Y-m-d'));
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

    // /**
    //  * Get Calendar
    //  * 
    //  * @return \Illuminate\Database\Eloquent\Collection
    //  */
    // public function getCalendar()
    // {
    //     if (request()->has('date')) {
    //         $date = request()->get('date');
    //         $this->calendars = $this->dateFilter($date, $this->calendars);
    //     }

    //     return $this->calendars->where(function ($query) {
    //         $query->where('user_id', Auth::user()->id)->orWhere('user_id', 0);
    //     })->orderBy('date')->get()->makeHidden(['created_at', 'updated_at']);
    // }
}
