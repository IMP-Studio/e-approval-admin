<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Employee;
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
        $employee = Employee::all();
        $division = Division::all();
        return view('home',compact('employee','division'));
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
