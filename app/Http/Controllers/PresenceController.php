<?php

namespace App\Http\Controllers;

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

        if ($request->ajax()) {
            $query = $request->input('query');
            $gabungData = $gabungData->filter(function ($item) use ($query) {
                // Ganti ini dengan logika pencarian sesuai dengan kebutuhan Anda
                return stripos($item->user->name, $query) !== false ||
                       stripos($item->category, $query) !== false;
            });
        
            $perPage = 5;
            $currentPage = $request->input('page', 1);
    
            $presenceData = new LengthAwarePaginator(
                $gabungData->forPage($currentPage, $perPage),
                $gabungData->count(),
                $perPage,
                $currentPage
            );
    
            $presenceData->setPath('');

            $output = '';
            $iteration = 0; 
            foreach ($presenceData as $item) {
                $iteration++;

                $output .= '<tr class="intro-x h-16">' .
                    '<td class="w-4 text-center">' .
                    $iteration .
                    '</td>' .
                    '<td class="flex justify-center align-center">' .
                    '<div class="w-12 h-12 image-fit zoom-in">';
                if ($item->user->employee->avatar) {
                    $output .= '<img data-action="zoom" class="rounded-full" src="'.asset('storage/'.$item->user->employee->avatar).'">';
                } elseif ($item->user->employee->gender == 'male') {
                    $output .= '<img data-action="zoom" class="rounded-full" src="'.asset('images/default-boy.jpg').'">';
                } elseif ($item->user->employee->gender == 'female') {
                    $output .= '<img data-action="zoom" class="rounded-full" src="'.asset('images/default-women.jpg').'">';
                }
                $output .= '</div>' .
                    '</td>' .
                    '<td class="w-50 text-center">' .
                    $item->user->name .
                    '</td>' .
                    '<td class="text-center capitalize">' .
                    $item->entry_time .
                    '</td>' .
                    '<td class="text-center capitalize">' .
                    ($item->category === 'work_trip' ? 'Work Trip' : $item->category) .
                    '</td>' .
                    '<td class="table-report__action w-56">' .
                    '<div class="flex justify-center items-center">' ;
                    if($item->category === 'WFO') {                                   
                        $output .= '<a class="flex items-center text-success delete-button mr-3 show-attendance-modal-search-wfo" data-avatar="' . $item->user->employee->avatar . '" data-gender="' . $item->user->employee->gender . '" data-firstname="' . $item->user->employee->first_name . '" data-LastName="' . $item->user->employee->last_name . '" data-stafId="' . $item->user->employee->id_number . '" data-Category="' . ($item->category === 'work_trip' ? 'Work Trip' : $item->category) . '" data-Position="' . $item->user->employee->position->name . '" data-entryTime="' . $item->entry_time . '" data-exitTime="' . $item->exit_time . '" href="javascript:;" data-tw-toggle="modal" data-tw-target="#show-modal-search-wfo">' .
                            '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="eye" data-lucide="eye" class="lucide lucide-eye w-4 h-4 mr-1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> Detail' .
                        '</a>';
                    }
                    elseif($item->category == 'telework'){
                        $output .= '<a class="flex items-center text-success delete-button mr-3 show-attendance-modal-search-telework" data-avatar="' . $item->user->employee->avatar . '" data-gender="' . $item->user->employee->gender . '" data-firstname="' . $item->user->employee->first_name . '" data-LastName="' . $item->user->employee->last_name . '" data-stafId="' . $item->user->employee->id_number . '" data-Category="' . ($item->category === 'work_trip' ? 'Work Trip' : $item->category) . '" data-Position="' . $item->user->employee->position->name . '" data-teleCategory="' . $item->telework->telework_category . '" data-tempoEntry="' . $item->temporary_entry_time . '" data-catDesc="' . $item->telework->category_description . '" href="javascript:;" data-tw-toggle="modal" data-tw-target="#show-modal-search-telework">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="eye" data-lucide="eye" class="lucide lucide-eye w-4 h-4 mr-1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> Detail
                        </a>';
                    }                    
                    elseif($item->category == 'work_trip'){
                    $output .= '<a class="flex items-center text-success delete-button mr-3 show-attendance-modal-search-worktrip" data-avatar="'. $item->user->employee->avatar .'" data-gender="'.$item->user->employee->gender.'" data-firstname="'.$item->user->employee->first_name.'" data-LastName="'. $item->user->employee->last_name.'" data-stafId="'.$item->user->employee->id_number.'" data-Category="'. ($item->category === 'work_trip' ? 'Work Trip' : $item->category) .'" data-Position="'. $item->user->employee->position->name .'" data-startDate="'. $item->start_date .'" data-endDate="'. $item->end_date .'" data-enrtyDate="'. $item->entry_date .'" href="javascript:;" data-tw-toggle="modal" data-tw-target="#show-modal-search-worktrip">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="eye" data-lucide="eye" class="lucide lucide-eye w-4 h-4 mr-1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> Detail
                    </a>';
                    }
                    elseif ($item->category == 'leave') {
                        $output .= '<a class="flex items-center text-success delete-button mr-3 show-attendance-modal-search-leave" data-avatar="' . $item->user->employee->avatar . '" data-gender="' . $item->user->employee->gender . '" data-firstname="' . $item->user->employee->first_name . '" data-LastName="' . $item->user->employee->last_name . '" data-stafId="' . $item->user->employee->id_number . '" data-Category="' . ($item->category === 'work_trip' ? 'Work Trip' : $item->category) . '" data-Position="' . $item->user->employee->position->name . '" data-startDate="' . $item->start_date . '" data-endDate="' . $item->end_date . '" data-entryDate="' . $item->entry_date . '" data-typeLeave="' . $item->type . '" data-typeDesc="' . $item->type_description . '" data-submisDate="' . $item->submission_date . '" data-totalDays="' . $item->total_leave_days . '" href="javascript:;" data-tw-toggle="modal" data-tw-target="#show-modal-search-leave">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="eye" data-lucide="eye" class="lucide lucide-eye w-4 h-4 mr-1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> Detail
                        </a>';
                    }                    
                    $output .=
                    '<a data-id="'. $item->id .'" data-name="'. $item->user->employee->first_name.' '.$item->user->employee->last_name .'"  class="flex items-center text-danger delete-modal-search-presence" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal-search-presence">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="trash-2" data-lucide="trash-2" class="lucide lucide-trash-2 w-4 h-4 mr-1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> Delete
                    </a>';
                    '</div>' .
                    '</td>' .
                    '</tr>';
            }

            return response($output);

        }

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
