<?php

namespace App\Http\Controllers;

use App\Exports\PresenceByRangeExport;
use App\Exports\PresenceExport;
use App\Exports\PresenceSheet;
use App\Models\Leave;
use App\Models\Presence;
use App\Models\StandUp;
use App\Models\StatusCommit;
use App\Models\Telework;
use App\Models\User;
use App\Models\WorkTrip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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


        if(request()->ajax()) {
            $startDateDmy = $request->input('start_date');
            $endDateDmy = $request->input('end_date');

            $startDate = Carbon::createFromFormat('d M, Y', $startDateDmy)->format('Y-m-d');
            $endDate = Carbon::createFromFormat('d M, Y', $endDateDmy)->format('Y-m-d');

            $tele_wfo_wk = Presence::whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->whereIn('category', ['WFO', 'telework', 'work_trip'])
            ->where(function ($query) use ($allowedStatusCheck) {
                $query->where('category', 'WFO')
                    ->orWhere(function ($query) use ($allowedStatusCheck) {
                        $query->where('category', 'telework')
                            ->whereHas('telework.statusCommit', $allowedStatusCheck);
                    })
                    ->orWhere(function ($query) use ($allowedStatusCheck) {
                        $query->where('category', 'work_trip')
                            ->whereHas('worktrip.statusCommit', $allowedStatusCheck);
                    });
            })
            ->with([
                'telework.statusCommit' => $allowedStatusCheck,
                'worktrip.statusCommit' => $allowedStatusCheck,
                'user', 
                'user.employee', 
                'user.employee.position', 
            ])
            ->orderBy('date', 'desc')
            ->orderBy('entry_time', 'desc')
            ->get();
    
            $leaveData = Leave::whereDate('start_date', '<=', $startDate)
            ->whereDate('end_date', '>=', $endDate)
                ->whereHas('statusCommit', $allowedStatusCheck)
                ->join('presences', 'leaves.presence_id', '=', 'presences.id')
                ->with([
                    'user',
                    'leavedetail.typeofleave',
                    'user.employee', 
                    'user.employee.position', 
                ])
                ->orderBy('presences.date', 'desc')
                ->orderBy('presences.entry_time', 'desc')
                ->get()
                ->map(function ($item) {
                    $item->category = 'leave';
                    return $item;
            });
            
            $mergeData = $tele_wfo_wk->concat($leaveData);

            $presenceData = $mergeData->sortByDesc('id')->values();
            

            return response()->json($presenceData);
        }else {
            $tele_wfo_wk = Presence::whereDate('date', $today)
            ->whereIn('category', ['WFO', 'telework', 'work_trip'])
            ->where(function ($query) use ($allowedStatusCheck) {
                $query->where('category', 'WFO')
                    ->orWhere(function ($query) use ($allowedStatusCheck) {
                        $query->where('category', 'telework')
                            ->whereHas('telework.statusCommit', $allowedStatusCheck);
                    })
                    ->orWhere(function ($query) use ($allowedStatusCheck) {
                        $query->where('category', 'work_trip')
                            ->whereHas('worktrip.statusCommit', $allowedStatusCheck);
                    });
            })
            ->with([
                'telework.statusCommit' => $allowedStatusCheck,
                'worktrip.statusCommit' => $allowedStatusCheck,
            ])
            ->orderBy('entry_time', 'desc')
            ->get();
    
            $leaveData = Leave::whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->whereHas('statusCommit', $allowedStatusCheck)
                ->join('presences', 'leaves.presence_id', '=', 'presences.id')
                ->orderBy('presences.date', 'desc')
                ->orderBy('presences.entry_time', 'desc')
                ->get()
                ->map(function ($item) {
                    $item->category = 'leave';
                    return $item;
            });
            
            $mergeData = $tele_wfo_wk->concat($leaveData);

            $sortedMergeData = $mergeData->sortByDesc('id');
    
            $presenceData = $sortedMergeData;
        }

        return view('attendance', compact('presenceData', 'today'));

    }

    public function exportExcel($year)
    {
        return Excel::download(new PresenceExport($year), "Presence $year.xlsx");
    }


    public function exportExcelByRange(Request $request)
    {
        
        $startDate = Carbon::createFromFormat('d M, Y', $request->startDate)->format('Y-m-d');
        $endDate = Carbon::createFromFormat('d M, Y', $request->endDate)->format('Y-m-d');
        
        return Excel::download(new PresenceByRangeExport($startDate, $endDate), "Presence.xlsx");
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
    public function destroy($id, Request $request)
    {
        $presence = Presence::findOrFail($id);

        $stand_up = StandUp::Where('presence_id', $presence->id)->first();

        $inputName= $request->input('validName');

        if($inputName === $presence->user->name){
            $presence->delete();
            if ($stand_up !== null) {
                $stand_up->delete();
            }
            $namapresence = $presence->user->name;
            return redirect()->back()->with(['delete' => "$namapresence deleted successfully"]);
        }else{
            return redirect()->back()->with(['error' => 'Username salah']);
        }
    }
}