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
        $today = Carbon::now()->setTimezone('Asia/Jakarta')->format('Y-m-d');

        $presence_today = Presence::whereDate('date',$today)->get()->count();
        $total_employee = Employee::count();

        $telework_today = Telework::whereHas('presence',
        function ($query) use ($today) {
            $query->whereDate('date', $today);
        })->get();

        $workTrip_today = WorkTrip::whereHas('presence',
        function ($query) use ($today) {
            $query->whereDate('date', $today);
        })->get();

        $leave_today = Leave::whereHas('presence',
        function ($query) use ($today) {
            $query->whereDate('date', $today);
        })->get();


        $attendance_percentage = $total_employee > 0 ? round(($presence_today / $total_employee) * 100, 1) : 0;
        $telework_percentage = $total_employee > 0 ? round(($telework_today->count() / $total_employee) * 100, 1) : 0;
        $workTrip_percentage = $total_employee > 0 ? round(($workTrip_today->count() / $total_employee) * 100, 1) : 0;
        $leave_percentage = $total_employee > 0 ? round(($leave_today->count() / $total_employee) * 100, 1) : 0;


        return view('home',compact('presence_today','telework_today','workTrip_today','leave_today','attendance_percentage',
        'telework_percentage','workTrip_percentage','leave_percentage'));
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
