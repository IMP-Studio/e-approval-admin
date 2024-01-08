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

            $workTrip_today = WorkTrip::whereHas('presence',
            function ($query) use ($now) {
                $query->whereDate('date', $now);
            })->whereHas('statusCommit', function ($query) {
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
        $subYear = $today->copy()->subYear()->year;

        $months = [
            'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
        ];

        $allowedStatusCheck = function ($query) {
            $query->where('status', 'allowed');
        };

        if (request()->ajax()) {
            $startDateDmy = $request->input('start_date');
            $endDateDmy = $request->input('end_date');

            $startDate = Carbon::createFromFormat('d M, Y', $startDateDmy)->format('Y-m-d');
            $endDate = Carbon::createFromFormat('d M, Y', $endDateDmy)->format('Y-m-d');

            $standup_today = StandUp::where(function ($query) use ($startDate,$endDate,$allowedStatusCheck) {
                $query->whereHas('presence', function ($subQuery) use ($startDate,$endDate,) {
                    $subQuery->whereIn('category', ['WFO', 'telework', 'work_trip'])
                    ->where(function ($statusQuery) use ($startDate,$endDate,) {
                        $statusQuery->whereDate('date', '>=', $startDate)
                        ->whereDate('date', '<=', $endDate);
                    });
                });
                $query->orWhereHas('presence', function ($subQuery) use ($startDate,$endDate, $allowedStatusCheck) {
                    $subQuery
                    ->whereIn('category', ['telework', 'work_trip'])
                    ->where(function ($statusQuery) use ($allowedStatusCheck) {
                        $statusQuery->whereHas('telework.statusCommit', $allowedStatusCheck)
                            ->orWhereHas('worktrip.statusCommit', $allowedStatusCheck);
                    })
                    ->where(function ($statusQuery) use ($startDate,$endDate,) {
                        $statusQuery->whereDate('date', '>=', $startDate)
                        ->whereDate('date', '<=', $endDate);
                    });
                });
            })
            ->with([
                'presence',
                'user',
                'user.employee',
                'user.employee.position',
                'project', 
            ])
            ->orderBy('id', 'desc')
            ->get();

            return response()->json($standup_today);
        }else {          
            $standup_today = StandUp::where(function ($query) use ($today, $allowedStatusCheck) {
                $query->whereHas('presence', function ($subQuery) use ($today) {
                    $subQuery->whereIn('category', ['WFO', 'telework', 'work_trip'])
                        ->whereDate('date', $today);
                });
                $query->orWhereHas('presence', function ($subQuery) use ($today, $allowedStatusCheck) {
                    $subQuery->whereDate('date', $today)
                    ->whereIn('category', ['telework', 'work_trip'])
                    ->where(function ($statusQuery) use ($allowedStatusCheck) {
                        $statusQuery->whereHas('telework.statusCommit', $allowedStatusCheck)
                            ->orWhereHas('worktrip.statusCommit', $allowedStatusCheck);
                    });
                });
            })
            ->orderBy('id', 'desc')
            ->get();
        }

        return view('standup', compact('standup_today', 'today', 'months', 'subYear'));
    }

    protected function getMonthName($month)
    {
        return date('F', mktime(0, 0, 0, $month, 1));
    }

    public function exportStandup($year, $month)
    {
        ini_set('max_execution_time', 300);
        $monthName = $this->getMonthName($month);
        return Excel::download(new StandupExport($year, $month), "Standup $monthName $year.xlsx");
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
