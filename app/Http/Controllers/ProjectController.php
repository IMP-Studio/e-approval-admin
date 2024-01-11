<?php

namespace App\Http\Controllers;

use App\Exports\ProjectExport;
use App\Imports\ProjectImport;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Partner;
use App\Models\Project;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $project = Project::orderBy('end_date', 'desc')->orderBy('id', 'desc')->get();
        $partnerall = Partner::all();

        $contributors = [];
        foreach ($project as $item) {
            $contributorsInfo = User::whereIn('id', $item->standups()->distinct()->pluck('user_id')->toArray())
            ->with('employee')
            ->select('id', 'name')
            ->get()
            ->toArray();

        // Map the data to include only necessary information
        $contributorsInfo = array_map(function ($contributor) {
            return [
                'id' => $contributor['id'],
                'name' => $contributor['name'],
                'avatar' => $contributor['employee']['avatar'] ?? null,
                'gender' => $contributor['employee']['gender'] ?? null,
            ];
        }, $contributorsInfo);


            $contributors[$item->id] = $contributorsInfo;
        }

        return view('project.index', compact('project', 'partnerall', 'contributors'));
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
            return redirect()->route('project')->with(['error' => "Failed to add project"]);
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

    /**
     * @return mixed
     */
    public function downloadTemplate()
    {
        $file_path = public_path("import/Template-Project.xlsx");

        if (file_exists($file_path)) {
            return response()->download($file_path);;
        } else {
            return redirect('/project')->with('error', 'File not found.');
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importExcel(Request $request)
    {
        try {
            $this->validate($request, [
                'import_file' => 'required|mimes:csv,xls,xlsx'
            ]);

            $file = $request->file('import_file');

            // @phpstan-ignore-next-line
            $nama_file = rand() . $file->getClientOriginalName();

            // @phpstan-ignore-next-line
            $file->move('storage/import', $nama_file);

            Excel::import(new ProjectImport, public_path('storage/import/' . $nama_file));

            return redirect('/project')->with('success', 'Data imported successfully');
        } catch (\Throwable $th) {
            return redirect('/project')->with('error', 'Make sure there is no duplicate data');
        }
    }

    public function exportExcel()
    {
        return Excel::download(new ProjectExport, 'Data-Project.xlsx');
    }
}
