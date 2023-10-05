<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Partner;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $employee = Employee::findOrFail(2);
        $project = Project::paginate(5);
        $partnerall = Partner::all();

        if ($request->ajax()) {
            $query = $request->input('query');
            $project = Project::where('name', 'LIKE', '%' . $query . '%')->paginate(5);

            $output = '';
            $iteration = 0;

            foreach ($project as $item) {
                $iteration++;
                $output .= '<tr class="intro-x h-16">
                                <td class="w-4 text-center">
                                    ' . $iteration . '
                                </td>
                                <td class="w-50 text-center capitalize">
                                    ' . $item->name . '
                                </td>
                                <td class="w-50 text-center capitalize">
                                    ' . $item->partner->name . '
                                </td>
                                <td class="w-50 text-center capitalize">';

                if ($item->end_date > now()) {
                    $output .= 'Active.';
                } elseif ($item->end_date < now()) {
                    $output .= 'Inactive.';
                }

                $output .= '
                                </td>
                                <td class="table-report__action w-56">
                                    <div class="flex justify-center items-center">
                                        <a data-projectId="' . $item->id . '" data-projectName="' . $item->name . '"
                                            data-endDate="' . $item->end_date . '" data-startDate="' . $item->start_date . '"
                                            data-projectpartnerId="' . $item->partner_id . '"
                                            data-partnerId="' . $item->partner->id . '"
                                            data-partnerName="' . $item->partner->name . '"
                                            class="flex items-center text-warning mr-3 edit-modal-project-search"
                                            href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-edit-project">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="check-square" data-lucide="check-square" class="lucide lucide-check-square w-4 h-4 mr-1"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg> Edit
                                        </a>

                                        <a data-projectnameD="'. $item->name .'" data-partnerNameD="'. $item->partner->name .'" data-startdateD="'. $item->start_date .'" data-enddateD="'. $item->end_date .'"
                                            class="mr-3 flex items-center text-success detail-project-modal-search"
                                            href="javascript:;" data-tw-toggle="modal"
                                            data-tw-target="#detail-project-modal">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="eye" data-lucide="eye" class="lucide lucide-eye w-4 h-4 mr-1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>';
            }

            return response($output);
        }

        return view('project.index', compact('project', 'partnerall', 'employee'));
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
            dd($input);
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
        try {
            $data = $request->all();
            $record = Project::find($id);

            $start_date = $record->start_date; // Default value
            $end_date = $record->end_date; // Default value
    
            // Periksa apakah tanggal berubah
            if ($request->start_date !== $record->start_date) {
                $start_date = Carbon::createFromFormat('d M, Y', $data['start_date'])->format('Y-m-d');
            }
            if ($request->end_date !== $record->end_date) {
                $end_date = Carbon::createFromFormat('d M, Y', $data['end_date'])->format('Y-m-d');
            }
    
                
    
            if (!$record) {
                return redirect()->back()->with(['error' => 'Data not found']);
            }
    
            // dd($data);

            $record->update([
                'name' => $data['name'],
                'partner_id' => $data['partner_id'],
                'start_date' => $start_date,
                'end_date' =>  $end_date,
            ]);

            return redirect()->route('project')->with(['success' => "$record->name updated successfully"]);
        } catch (\Throwable $th) {
            return redirect()->route('project')->with(['error' => "Failed to update project"]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
