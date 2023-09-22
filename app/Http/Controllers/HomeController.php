<?php

namespace App\Http\Controllers;

use App\Exports\StandupExport;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Presence;
use App\Models\StandUp;
use App\Models\StatusCommit;
use App\Models\Telework;
use App\Models\WorkTrip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $now = Carbon::now()->setTimezone('Asia/Jakarta');
        $year = $now->format('Y');
        $total_employee = Employee::count();

        $allowedStatusCheck = function ($query) {
            $query->where('status', 'allowed');
        };
        // START COUNT DATA CHECK-IN TODAY
            $presenceDataCount = Presence::whereDate('date', $now)
                ->whereIn('category', ['WFO', 'telework', 'leave', 'work_trip'])
                ->where(function ($query) use ($allowedStatusCheck) {
                    $query->where('category', 'WFO')
                        ->orWhere(function($query) use ($allowedStatusCheck) {
                            $query->where('category', 'work_trip')
                                    ->whereHas('worktrip.statusCommit', $allowedStatusCheck);
                        })
                        ->orWhere(function($query) use ($allowedStatusCheck) {
                            $query->where('category', 'leave')
                                    ->whereHas('leave.statusCommit', $allowedStatusCheck);
                        })
                        ->orWhere(function($query) use ($allowedStatusCheck) {
                            $query->where('category', 'telework')
                                    ->whereHas('telework.statusCommit', $allowedStatusCheck);
                        });
                })
                ->with([
                    'worktrip.statusCommit' => $allowedStatusCheck,
                    'leave.statusCommit' => $allowedStatusCheck,
                    'telework.statusCommit' => $allowedStatusCheck,
            ])
            ->count();
        // END COUNT DATA CHECK-IN TODAY

        // START PRESENCE TODAY
            $wfo_today = Presence::whereDate('date', $now)->where('category','WFO')->get();

            $telework_today = Telework::whereHas('presence',
                function ($query) use ($now) {
                    $query->whereDate('date', $now);
                })->whereHas('statusCommit', function ($query) {
                    $query->where('status', 'allowed');
            })->get();

            $workTrip_today = WorkTrip::whereDate('start_date', '<=', $now)
                ->whereDate('end_date', '>=', $now)
                ->whereHas('statusCommit', function ($query) {
                    $query->where('status', 'allowed');
            })->get();

            $leave_today = Leave::whereDate('start_date', '<=', $now)
                ->whereDate('end_date', '>=', $now)
                ->whereHas('statusCommit', function ($query) {
                    $query->where('status', 'allowed');
            })->get();
        // END PRESENCE TODAY

        // START PRESENCE MONTH STATUS ALLOWED
            for ($i = 1; $i <= 12; $i++) {
                $month = str_pad($i, 2, '0', STR_PAD_LEFT);

                $wfo_month = Presence::whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->where('category', 'WFO') // Cari data dengan kategori "wfo"
                    ->count();
                $wfo_data[] = $wfo_month;

                $telework_month = Telework::whereHas('presence', function ($query) use ($month, $year) {
                    $query->whereMonth('date', $month)
                        ->whereYear('date', $year);
                    })->whereHas('statusCommit', function ($query) {
                        $query->where('status', 'allowed');
                })->count();
                $telework_data[] = $telework_month;

                $workTrip_month = WorkTrip::whereHas('presence', function ($query) use ($month, $year) {
                    $query->whereMonth('date', $month)
                        ->whereYear('date', $year);
                    })->whereHas('statusCommit', function ($query) {
                        $query->where('status', 'allowed');
                    })->count();
                $workTrip_data[] = $workTrip_month;

                $leave_month = Leave::whereHas('presence', function ($query) use ($month, $year) {
                    $query->whereMonth('date', $month)
                        ->whereYear('date', $year);
                    })->whereHas('statusCommit', function ($query) {
                        $query->where('status', 'allowed');
                })->count();
                $leave_data[] = $leave_month;
            }
        // END PRESENCE MONTH STATUS ALLOWED

        // START PRESENCE MONTH STATUS REJECTED
            for ($i = 1; $i <= 12; $i++) {
                $month = str_pad($i, 2, '0', STR_PAD_LEFT);

                $telework_month_rejected = Telework::whereHas('presence', function ($query) use ($month, $year) {
                    $query->whereMonth('date', $month)
                        ->whereYear('date', $year);
                    })->whereHas('statusCommit', function ($query) {
                        $query->where('status', 'rejected');
                })->count();
                $telework_data_month_rejected[] = $telework_month_rejected;

                $workTrip_month_rejected = WorkTrip::whereHas('presence', function ($query) use ($month, $year) {
                    $query->whereMonth('date', $month)
                        ->whereYear('date', $year);
                    })->whereHas('statusCommit', function ($query) {
                        $query->where('status', 'rejected');
                })->count();
                $workTrip_data_month_rejected[] = $workTrip_month_rejected;

                $leave_month_rejected = Leave::whereHas('presence', function ($query) use ($month, $year) {
                    $query->whereMonth('date', $month)
                        ->whereYear('date', $year);
                    })->whereHas('statusCommit', function ($query) {
                        $query->where('status', 'rejected');
                })->count();
                $leave_data_month_rejected[] = $leave_month_rejected;
            }
        // END PRESENCE MONTH STATUS REJECTED

        // START PRESENCE YEARLY STATUS ALLOWED
            $wfo_yearly = Presence::whereYear('date', $year)
                    ->where('category', 'WFO') // Cari data dengan kategori "wfo"
                    ->count();

            $telework_yearly = Telework::whereHas('presence',
                function ($query) use ($year) {
                    $query->whereYear('date', $year);
                })->whereHas('statusCommit', function ($query) {
                    $query->where('status', 'allowed');
            })->count();

            $workTrip_yearly = WorkTrip::whereHas('presence',
                function ($query) use ($year) {
                    $query->whereYear('date', $year);
                })->whereHas('statusCommit', function ($query) {
                    $query->where('status', 'allowed');
            })->count();

            $leave_yearly = Leave::whereHas('presence',
                function ($query) use ($year) {
                    $query->whereYear('date', $year);
                })->whereHas('statusCommit', function ($query) {
                    $query->where('status', 'allowed');
            })->count();
        // END PRESENCE YEARLY STATUS ALLOWED

        // START PRESENCE YEARLY STATUS REJECTED
            $telework_rejected = Telework::whereHas('presence',
                function ($query) use ($year) {
                    $query->whereYear('date', $year);
                })->whereHas('statusCommit', function ($query) {
                    $query->where('status', 'rejected');
            })->count();

            $workTrip_rejected = WorkTrip::whereHas('presence',
                function ($query) use ($year) {
                    $query->whereYear('date', $year);
                })->whereHas('statusCommit', function ($query) {
                    $query->where('status', 'rejected');
            })->count();

            $leave_rejected = Leave::whereHas('presence',
                function ($query) use ($year) {
                    $query->whereYear('date', $year);
                })->whereHas('statusCommit', function ($query) {
                    $query->where('status', 'rejected');
            })->count();
        // END PRESENCE YEARLY STATUS REJECTED

        // START PERCENTAGE DATA
            $attendance_percentage = $total_employee > 0 ? round(($presenceDataCount / $total_employee) * 100, 1) : 0;
            $telework_percentage = $total_employee > 0 ? round(($telework_today->count() / $total_employee) * 100, 1) : 0;
            $workTrip_percentage = $total_employee > 0 ? round(($workTrip_today->count() / $total_employee) * 100, 1) : 0;
            $leave_percentage = $total_employee > 0 ? round(($leave_today->count() / $total_employee) * 100, 1) : 0;
        // END PERCENTAGE DATA

        return view('home',compact('presenceDataCount','wfo_today','telework_today','workTrip_today','leave_today','attendance_percentage',
        'telework_percentage','workTrip_percentage','leave_percentage','wfo_data','telework_data','workTrip_data','leave_data',
        'wfo_yearly','telework_yearly','workTrip_yearly','leave_yearly','telework_rejected','workTrip_rejected','leave_rejected',
        'telework_data_month_rejected','workTrip_data_month_rejected','leave_data_month_rejected'));
    }

    public function standup(Request $request)
    {
        $today = Carbon::today()->setTimezone('Asia/Jakarta');
        $allowedStatusCheck = function ($query) {
            $query->where('status', 'allowed');
        };

        $tele_wfo = StandUp::where(function ($query) use ($today, $allowedStatusCheck) {
            $query->whereHas('presence', function ($subQuery) use ($today) {
                $subQuery->whereDate('date', $today)
                    ->where('category', 'WFO');
            });
            $query->orWhereHas('presence', function ($subQuery) use ($today, $allowedStatusCheck) {
                $subQuery->whereDate('date', $today)
                    ->where('category', 'telework')
                    ->whereHas('telework.statusCommit', $allowedStatusCheck);
            });
        })->get();

        $workTripData = StandUp::whereHas('presence', function ($query) use ($today) {
            $query->where('category', 'work_trip');
        })
        ->whereHas('presence.worktrip', function ($query) use ($today) {
            $query->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->whereHas('statusCommit', function ($query) {
                    $query->where('status', 'allowed');
                });
        })
        ->get();

        $gabungData = $tele_wfo->concat($workTripData);
        $perPage = 5;
        $currentPage = $request->input('page', 1);

        if ($request->ajax()) {
            $query = $request->input('query');
            $gabungData = $gabungData->filter(function ($item) use ($query) {
                return stripos($item->user->name, $query) !== false;
            });

            $perPage = 5;
            $currentPage = $request->input('page', 1);
            

            $standup_today = new LengthAwarePaginator(
                $gabungData->forPage($currentPage, $perPage),
                $gabungData->count(),
                $perPage,
                $currentPage
            );
            
            $standup_today->setPath('');

            $output = '';
            $iteration = 0; 

            foreach ($standup_today as $item) {
                $iteration++;
        
                $output .= '<tr class="intro-x h-16">' .
                    '<td class="w-4 text-center">' .
                    $iteration .
                    '</td>' .
                    '<td class="w-50 text-center">' .
                    $item->user->name .
                    '</td>' .
                    '<td class="text-center capitalize">' .
                    $item->user->employee->position->name .
                    '</td>' .
                    '<td class="text-start">' .
                    $item->project->name .
                    '</td>' .
                    '<td class="text-center">' .
                    $item->doing .
                    '</td>' .
                    '<td class="w-40 text-center text-warning">' .
                    ($item->blocker ? $item->blocker : "-") .
                    '</td>' .
                    '<td class="table-report__action w-56">' .
                    '<div class="flex justify-center items-center">' .
                    '<a class="flex items-center text-success delete-button mr-3" href="javascript:;" data-tw-toggle="modal" data-tw-target="#detail-' . $item->id . '-modal">' .
                    '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="eye" data-lucide="eye" class="lucide lucide-eye w-4 h-4 mr-1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> Detail' .
                    '</a>' .
                    '<a class="flex items-center text-danger delete-button" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal-' . $item->id . '">' .
                    '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="eye" data-lucide="eye" class="lucide lucide-eye w-4 h-4 mr-1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> Delete' .
                    '</a>' .        
                    '</div>' .
                    '</td>' .
                    '</tr>' .
                    
                    '<div id="detail-' . $item->id . '-modal" class="modal" tabindex="-1" aria-hidden="true">' .
                    '<div class="modal-dialog modal-lg">' .
                    '<div class="modal-content">' .
                    '<div class="modal-header">' .
                    '<h1 class="font-medium text-base mx-auto">Detail Standup ' . $item->user->name . '</h1>' .
                    '</div>' .
                    '<div class="modal-body grid grid-cols-12 gap-4 gap-y-3">' .
                    '<div class="col-span-12">' .
                    '<label for="modal-form-1" class="form-label">Done :</label>' .
                    '<textarea disabled name="" class="form-control" id="" rows="3">' . $item->done . '</textarea>' .
                    '</div>' .
                    '<div class="col-span-12">' .
                    '<label for="modal-form-2" class="form-label">Doing :</label>' .
                    '<textarea disabled name="" class="form-control" id="" rows="3">' . $item->doing . '</textarea>' .
                    '</div>';
        
                if ($item->blocker) {
                    $output .=
                        '<div class="col-span-12">' .
                        '<label for="modal-form-2" class="form-label">Blocker :</label>' .
                        '<textarea disabled name="" class="form-control" id="" rows="3">' . $item->blocker . '</textarea>' .
                        '</div>';
                }
        
                $output .=
                    '</div>' .
                    '</div>' .
                    '</div>' .
                    '</div>' .
                    
                    '<div id="delete-confirmation-modal-' . $item->id . '" class="modal" tabindex="-1" aria-hidden="true">' .
                    '<div class="modal-dialog">' .
                    '<div class="modal-content">' .
                    '<form id="delete-form" method="POST" action="' . route('standup.destroy', $item->id) . '">' .
                    csrf_field() .
                    method_field('delete') .
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

        $standup_today = new LengthAwarePaginator(
            $gabungData->forPage($currentPage, $perPage),
            $gabungData->count(),
            $perPage,
            $currentPage
        );

        $standup_today->setPath('');

        return view('standup', compact('standup_today', 'today'));
    }

    public function exportStandup($year)
    {
        return Excel::download(new StandupExport($year), "Standup $year.xlsx");
    }
    public function back()
    {
        return redirect('/home');
    }

    public function boy()
    {
        $divisi = Division::all();
        return view('divisi.boy', compact('divisi'));
    }

    public function destroy($id, Request $request)
    {
        $standup = StandUp::findOrFail($id);

        $inputName= $request->input('validName');

        if($inputName === $standup->user->name){
            $standup->delete();
            $namapresence = $standup->user->name;
            return redirect()->back()->with(['delete' => "$namapresence deleted successfully"]);
        }else{
            return redirect()->back()->with(['error' => 'Username salah']);
        }
    }
}
