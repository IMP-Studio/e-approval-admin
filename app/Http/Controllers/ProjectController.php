<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Partner;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employee = Employee::findOrFail(2);
        $project = Project::paginate(5);
        $partnerall = Partner::all();

        return view('project.index',compact('project', 'partnerall', 'employee'));
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
        try {
            $input = $request->all();
            $start_date = Carbon::createFromFormat('d M, Y', $input['start_date'])->format('Y-m-d');
            $end_date = Carbon::createFromFormat('d M, Y', $input['end_date'])->format('Y-m-d');
            

            Project::create([
                'name' => $input['name'],
                'partner_id' => $input['partner_id'],
                'start_date' => $start_date,
                'end_date' => $end_date,
            ]);

            $user_name = $input['name'];
            return redirect()->route('project')->with(['success' => "$user_name added successfully"]);
        } catch (\Throwable $th) {
            return redirect()->route('project')->with(['error' => "Failed to add employee"]);
        }
    
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
