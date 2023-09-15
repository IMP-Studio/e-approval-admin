<?php

namespace App\Http\Controllers;

use App\Exports\PresenceExport;
use App\Exports\PresenceSheet;
use App\Models\Leave;
use App\Models\Presence;
use App\Models\StatusCommit;
use App\Models\Telework;
use App\Models\User;
use App\Models\WorkTrip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PresenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $today = Carbon::today('Asia/Jakarta');
        $allowedStatusCheck = function ($query) {
            $query->where('status', 'allowed');
        };

        $tele_wfo = Presence::whereDate('date', $today)
            ->whereIn('category', ['WFO', 'telework'])
            ->where(function ($query) use ($allowedStatusCheck) {
                $query->where('category', 'WFO')
                    ->orWhere(function($query) use ($allowedStatusCheck) {
                        $query->where('category', 'telework')
                            ->whereHas('telework.statusCommit', $allowedStatusCheck);
                    });
            })
            ->with([
                'telework.statusCommit' => $allowedStatusCheck,
            ])
            ->orderBy('entry_time','asc')
        ->get();
        $leaveData = Leave::whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->whereHas('statusCommit', $allowedStatusCheck)
            ->join('presences', 'leaves.presence_id', '=', 'presences.id')
            ->orderBy('presences.entry_time', 'asc')
            ->get()
            ->map(function ($item) {
                $item->category = 'leave';
                return $item;
        });

        $workTripData = WorkTrip::whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->whereHas('statusCommit', $allowedStatusCheck)
            ->join('presences', 'work_trips.presence_id', '=', 'presences.id')
            ->orderBy('presences.entry_time', 'asc')
            ->get()
            ->map(function ($item) {
                $item->category = 'work_trip';
                return $item;
        });


        $gabungData = $tele_wfo->concat($leaveData)->concat($workTripData);

        $perPage = 5;
        $currentPage = $request->input('page', 1);

        $presenceData = new LengthAwarePaginator(
            $gabungData->forPage($currentPage, $perPage),
            $gabungData->count(),
            $perPage,
            $currentPage
        );

        $presenceData->setPath('');

        return view('attendance', compact('presenceData', 'today'));

    }

    public function exportExcel($year)
    {
        return Excel::download(new PresenceExport($year), "Presence $year.xlsx");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
