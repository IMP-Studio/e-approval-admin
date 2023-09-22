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

             // Generate the HTML for the table's tbody
  // Generate the HTML for the table's tbody
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
        '<div class="flex justify-center items-center">' .
        '<a class="flex items-center text-success delete-button mr-3" href="javascript:;" data-tw-toggle="modal" data-tw-target="#detail-'.$item->id.'-modal">' .
        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="eye" data-lucide="eye" class="lucide lucide-eye w-4 h-4 mr-1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> Detail' .
        '</a>' .
        '<a class="flex items-center text-danger delete-button" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal-'.$item->id.'">' .
        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="eye" data-lucide="eye" class="lucide lucide-eye w-4 h-4 mr-1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> Delete' .
        '</a>' .        
        '</div>' .
        '</td>' .
        '</tr>' .

        '<div id="detail-'.$item->id.'-modal" class="modal" tabindex="-1" aria-hidden="true">' .
        '<div class="modal-dialog modal-lg">' .
        '<div class="modal-content">' .
        '<div class="modal-header">' .
        '<h2 class="font-medium text-lg mx-auto">Detail Kehadiran</h2>' .
        '</div>' .
        '<div class="modal-body grid grid-cols-12 gap-4 gap-y-3">' .
        '<div class="col-span-12 mx-auto">' .
        '<div class="w-24 h-24 image-fit zoom-in">';
    if ($item->user->employee->avatar) {
        $output .= '<img class="tooltip rounded-full" src="' . asset('storage/' . $item->user->employee->avatar) . '">';
    } elseif ($item->user->employee->gender == 'male') {
        $output .= '<img class="tooltip rounded-full" src="' . asset('images/default-boy.jpg') . '">';
    } elseif ($item->user->employee->gender == 'female') {
        $output .= '<img class="tooltip rounded-full" src="' . asset('images/default-women.jpg') . '">';
    }
    $output .= '</div>' .
        '</div>' .
        '<div class="col-span-12 sm:col-span-6">' .
        '<label for="modal-form-1" class="text-xs">Firstname :</label>' .
        '<input disabled id="modal-form-1" type="text" class="form-control" value="' . $item->user->employee->first_name . '">' .
        '</div>' .
        '<div class="col-span-12 sm:col-span-6">' .
        '<label for="modal-form-2" class="text-xs">Lastname :</label>' .
        '<input disabled id="modal-form-2" type="text" class="form-control" value="' . $item->user->employee->last_name . '">' .
        '</div>' .
        '<div class="col-span-12 sm:col-span-6">' .
        '<label for="modal-form-2" class="text-xs">Staff Id :</label>' .
        '<input disabled id="modal-form-2" type="text" class="form-control" value="' . $item->user->employee->id_number . '">' .
        '</div>' .
        '<div class="col-span-12 sm:col-span-6">' .
        '<label for="modal-form-2" class="text-xs">Position :</label>' .
        '<input disabled id="modal-form-2" type="text" class="form-control" value="' . $item->user->employee->position->name . '">' .
        '</div>' .
        '<div class="col-span-12 sm:col-span-6">' .
        '<label for="modal-form-1" class="text-xs">Category :</label>' .
        '<input disabled id="modal-form-1" type="text" class="form-control capitalize" value="' . ($item->category === 'work_trip' ? 'Work Trip' : $item->category) . '">' .
        '</div>';
    if ($item->category == 'WFO') {
        $output .=
            '<div class="col-span-12 sm:col-span-6">' .
            '<label for="modal-form-2" class="text-xs">Entry Time  :</label>' .
            '<input disabled id="modal-form-2" type="text" class="form-control" value="' . $item->entry_time . ' WIB">' .
            '</div>' .
            '<div class="col-span-12 sm:col-span-6">' .
            '<label for="modal-form-2" class="text-xs">Exit Time  :</label>' .
            '<input disabled id="modal-form-2" type="text" class="form-control" value="' . $item->exit_time . ' WIB">' .
            '</div>';
    } elseif ($item->category == 'telework') {
        $output .=
            '<div class="col-span-12 sm:col-span-6">' .
            '<label for="modal-form-2" class="text-xs">Telework Category  :</label>' .
            '<input disabled id="modal-form-2" type="text" class="form-control capitalize" value="' . $item->telework->telework_category . '">' .
            '</div>';
        if ($item->telework->category_description) {
            $output .=
                '<div class="col-span-12 sm:col-span-6">' .
                '<label for="modal-form-1" class="text-xs">Category Description :</label>' .
                '<input disabled id="modal-form-1" type="text" class="form-control capitalize" value="' . $item->telework->category_description . '">' .
                '</div>';
        }
        $output .=
            '<div class="col-span-12 sm:col-span-6">' .
            '<label for="modal-form-2" class="text-xs">Temporary Entry Time  :</label>' .
            '<input disabled id="modal-form-2" type="text" class="form-control capitalize" value="' . $item->temporary_entry_time . '">' .
            '</div>';
    } elseif ($item->category == 'work_trip') {
        $output .=
            '<div class="col-span-12 sm:col-span-6">' .
            '<label for="modal-form-1" class="text-xs">Start Date :</label>' .
            '<input disabled id="modal-form-1" type="text" class="form-control" value="' . $item->start_date . '">' .
            '</div>' .
            '<div class="col-span-12 sm:col-span-6">' .
            '<label for="modal-form-1" class="text-xs">End Date :</label>' .
            '<input disabled id="modal-form-1" type="text" class="form-control" value="' . $item->end_date . '">' .
            '</div>' .
            '<div class="col-span-12 sm:col-span-6">' .
            '<label for="modal-form-1" class="text-xs">Entry Date :</label>' .
            '<input disabled id="modal-form-1" type="text" class="form-control" value="' . $item->entry_date . '">' .
            '</div>';
    } elseif ($item->category == 'leave') {
        $output .=
            '<div class="col-span-12 sm:col-span-6">' .
            '<label for="modal-form-1" class="text-xs">Type Leave :</label>' .
            '<input disabled id="modal-form-1" type="text" class="form-control capitalize" value="' . $item->type . '">' .
            '</div>' .
            '<div class="col-span-12 sm:col-span-6">' .
            '<label for="modal-form-1" class="text-xs">Type Description :</label>' .
            '<input disabled id="modal-form-1" type="text" class="form-control" value="' . $item->type_description . '">' .
            '</div>' .
            '<div class="col-span-12 sm:col-span-6">' .
            '<label for="modal-form-1" class="text-xs">Submission Date :</label>' .
            '<input disabled id="modal-form-1" type="text" class="form-control" value="' . $item->submission_date . '">' .
            '</div>' .
            '<div class="col-span-12 sm:col-span-6">' .
            '<label for="modal-form-1" class="text-xs">Start Date :</label>' .
            '<input disabled id="modal-form-1" type="text" class="form-control" value="' . $item->start_date . '">' .
            '</div>' .
            '<div class="col-span-12 sm:col-span-6">' .
            '<label for="modal-form-1" class="text-xs">End Date :</label>' .
            '<input disabled id="modal-form-1" type="text" class="form-control" value="' . $item->end_date . '">' .
            '</div>' .
            '<div class="col-span-12 sm:col-span-6">' .
            '<label for="modal-form-1" class="text-xs">Total Leave Days :</label>' .
            '<input disabled id="modal-form-1" type="text" class="form-control" value="' . $item->total_leave_days . ' Days">' .
            '</div>' .
            '<div class="col-span-12 sm:col-span-6">' .
            '<label for="modal-form-1" class="text-xs">Entry Date :</label>' .
            '<input disabled id="modal-form-1" type="text" class="form-control" value="' . $item->entry_date . '">' .
            '</div>';
    }
    $output .=
        '</div>' .
        '</div>' .
        '</div>' .
        '</div>' .

        '<div id="delete-confirmation-modal-'.$item->id.'" class="modal" tabindex="-1" aria-hidden="true">' .
        '<div class="modal-dialog">' .
        '<div class="modal-content">' .
        '<form id="delete-form" method="POST" action="' . route('presence.destroy', $item->id) . '">' .
        '@csrf' .
        '@method("delete")' .
        '<div class="modal-body p-0">' .
        '<div class="p-5 text-center">' .
        '<i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>' .
        '<div class="text-3xl mt-5">Are you sure?</div>' .
        '<div class="text-slate-500 mt-2">' .
        'Please type the username "' . $item->user->employee->first_name . ' ' . $item->user->employee->last_name . '" of the data to confirm.' .
        '</div>' .
        '<input name="validName" id="crud-form-2" type="text" class="form-control w-full" placeholder="User name" required>' .
        '</div>' .
        '<div class="px-5 pb-8 text-center">' .
        '<button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-24 mr-1">Cancel</button>' .
        '<button type="submit" class="btn btn-danger w-24">Delete</button>' .
        '</div>' .
        '</div>' .
        '</form>' .
        '</div>' .
        '</div>' .
        '</div>';
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
