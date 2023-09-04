<?php

namespace App\Http\Controllers;

use App\Exports\DivisionExport;
use App\Exports\EmployeeExport;
use App\Http\Requests\profileRequest;
use App\Models\Position;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Component\Console\Input\Input;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Imports\DivisionImport;
use App\Imports\EmployeeImport;
use App\Models\Division;
use App\Models\Employee;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Illuminate\Contracts\Session\Session;
use PDF;
use Psy\Readline\Hoa\Console;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employee = Employee::paginate(5);
        return view('employee.index',compact('employee'));
    }

    public function create()
    {
        $division = Division::all();

        return view('employee.create',compact('division'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $input = $request->all();

            if ($image = $request->file('avatar')) {
                $destinationPath = 'images/';
                $profileImage = $image->getClientOriginalName();
                $image->storeAs($destinationPath, $profileImage);
                $input['avatar'] = $profileImage;
            }

            $user = User::create([
                'name' => $input['first_name'] . '-' . $input['last_name'],
                'email' => $request->email,
                'password' => $request->password,
            ]);

            Employee::create([
                'user_id' => $user->id,
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'avatar' => $input['avatar'],
                'id_number' => $input['id_number'],
                'division_id' => $input['division'],
                'position_id' => $input['position'],
                'gender' => $input['gender'],
                'address' => $input['address'],
                'birth_date' => $input['birth_date'],
                'is_active' => true
            ]);

            $user_name = $user->firstname;
            return redirect()->route('employee')->with(['success' => "$user_name added successfully"]);
        } catch (\Throwable $th) {
            return redirect()->route('employee')->with(['error' => "Failed to add employee"]);
        }


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
        $employee = Employee::findOrFail($id);
        $position = Position::all();
        return view('employee.edit',compact('employee','position'));
    }

    public function update(Request $request, $id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $old_image = $employee->avatar;

            $input = $request->all();

            if ($image = $request->file('avatar')) {
                $destinationPath = 'images/';
                $profileImage = $image->getClientOriginalName();
                $image->storeAs($destinationPath, $profileImage);
                $input['avatar'] = $profileImage;

                if ($old_image) {
                    Storage::delete('images/' . $old_image);
                }
            } else {
                $input['avatar'] = $old_image;
            }

            $employee->update([
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'id_number' => $input['id_number'],
                'avatar' => $input['avatar'],
                'position_id' => $input['position'],
                'gender' => $input['gender'],
                'address' => $input['address'],
                'birth_date' => $input['birth_date']
            ]);

            $employee_name = $employee->first_name;

            return redirect()->route('employee')->with(['updated' => "$employee_name added successfully"]);
        } catch (\Throwable $th) {
            return redirect()->route('employee')->with(['error' => "Failed to update employee"]);
        }
    }

    public function destroy(string $id)
    {
        try {
            $employee = Employee::findOrFail($id);
            if ($employee->avatar) {
                $imagePath = public_path('images/') . $employee->avatar;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $employee->delete();

            $user = User::where('id', $employee->user_id)->first();
            if ($user) {
                $user->delete();
            }

            $employee_name = $employee->firstname;

            return redirect()->back()->with(['delete' => "$employee_name deleted successfully"]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['error' => "Failed to delete employee"]);
        }

    }
    public function trash()
    {
        $employee = Employee::withTrashed()->onlyTrashed()->get();
        $user = User::withTrashed()->onlyTrashed()->get();
        return view('employee.trash',compact('employee','user'));
    }

    public function export_excel()
    {
        return Excel::download(new EmployeeExport, 'Data-Employee.xlsx');
    }

    public function export_pdf()
    {
        $employee = Employee::all();
        $division = Division::all();

    	$pdf = PDF::loadview('employee.export-pdf',compact('employee','division'));
    	return $pdf->download('Data-Employee.pdf');
    }

    public function import_excel(Request $request)
    {
        $this->validate($request, [
            'import_file' => 'required|mimes:csv,xls,xlsx'
        ]);

        $file = $request->file('import_file');

        $nama_file = rand().$file->getClientOriginalName();

        $file->move('export',$nama_file);

        Excel::import(new EmployeeImport, public_path('export/'.$nama_file));
        // Excel::import(new EmployeeImport,$request->file('import_file')->store('export'));

        return redirect('/employee');
    }
    public function getPositions($divisionId)
    {
        $positions = Position::where('division_id', $divisionId)->get();

        return response()->json(['data' => $positions]);
    }

}
