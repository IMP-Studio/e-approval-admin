<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Presence;
use App\Models\Telework;
use App\Models\WorkTrip;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        $today = $now->format('Y-m-d');
        $year = $now->format('Y');
        $total_employee = Employee::count();

        // PRESENCE HARI INI
        $presence_today = Presence::whereDate('date',$now)->get()->count();

        $telework_today = Telework::whereHas('presence',
        function ($query) use ($now) {
            $query->whereDate('date', $now);
        })->whereHas('statusCommit', function ($query) {
            $query->where('status', 'allowed')
                ->where('statusable_type', 'App\Models\Telework');
        })->get();

        $workTrip_today = WorkTrip::whereHas('presence',
        function ($query) use ($now) {
            $query->whereDate('date', $now);
        })->whereHas('statusCommit', function ($query) {
            $query->where('status', 'allowed')
                ->where('statusable_type', 'App\Models\WorkTrip');
        })->get();

        $leave_today = Leave::whereHas('presence',
        function ($query) use ($now) {
            $query->whereDate('date', $now);
        })->whereHas('statusCommit', function ($query) {
            $query->where('status', 'allowed')
                ->where('statusable_type', 'App\Models\Leave');
        })->get();

        // PRESENCE PER BULAN TAHUN INI
        $attendance_data = [];
        $telework_data = [];
        $workTrip_data = [];
        $leave_data = [];

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
                $query->where('status', 'allowed')
                    ->where('statusable_type', 'App\Models\Telework');
            })->count();
            $telework_data[] = $telework_month;

            $workTrip_month = WorkTrip::whereHas('presence', function ($query) use ($month, $year) {
                $query->whereMonth('date', $month)
                    ->whereYear('date', $year);
            })->whereHas('statusCommit', function ($query) {
                $query->where('status', 'allowed')
                    ->where('statusable_type', 'App\Models\WorkTrip');
            })->count();
            $workTrip_data[] = $workTrip_month;

            $leave_month = Leave::whereHas('presence', function ($query) use ($month, $year) {
                $query->whereMonth('date', $month)
                    ->whereYear('date', $year);
            })->whereHas('statusCommit', function ($query) {
                $query->where('status', 'allowed')
                    ->where('statusable_type', 'App\Models\Leave');
            })->count();
            $leave_data[] = $leave_month;
        }

        // PRESENCE TAHUN INI
        $wfo_yearly = Presence::whereYear('date', $year)
                ->where('category', 'WFO') // Cari data dengan kategori "wfo"
                ->count();

        $telework_yearly = Telework::whereHas('presence',
        function ($query) use ($year) {
            $query->whereYear('date', $year);
        })->whereHas('statusCommit', function ($query) {
            $query->where('status', 'allowed')
                ->where('statusable_type', 'App\Models\Telework');
        })->count();

        $workTrip_yearly = WorkTrip::whereHas('presence',
        function ($query) use ($year) {
            $query->whereYear('date', $year);
        })->whereHas('statusCommit', function ($query) {
            $query->where('status', 'allowed')
                ->where('statusable_type', 'App\Models\WorkTrip');
        })->count();

        $leave_yearly = Leave::whereHas('presence',
        function ($query) use ($year) {
            $query->whereYear('date', $year);
        })->whereHas('statusCommit', function ($query) {
            $query->where('status', 'allowed')
                ->where('statusable_type', 'App\Models\Leave');
        })->count();

        // PERSENTASE
        $attendance_percentage = $total_employee > 0 ? round(($presence_today / $total_employee) * 100, 1) : 0;
        $telework_percentage = $total_employee > 0 ? round(($telework_today->count() / $total_employee) * 100, 1) : 0;
        $workTrip_percentage = $total_employee > 0 ? round(($workTrip_today->count() / $total_employee) * 100, 1) : 0;
        $leave_percentage = $total_employee > 0 ? round(($leave_today->count() / $total_employee) * 100, 1) : 0;


        return view('home',compact('presence_today','telework_today','workTrip_today','leave_today','attendance_percentage',
        'telework_percentage','workTrip_percentage','leave_percentage','wfo_data','telework_data','workTrip_data','leave_data',
        'wfo_yearly','telework_yearly','workTrip_yearly','leave_yearly'));
    }

    // public function kehadiran()
    // {
    //     return view('kehadiran');
    // }

    public function cuti()
    {
        return view('cuti');
    }

    public function standup()
    {
        return view('standup');
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
}
