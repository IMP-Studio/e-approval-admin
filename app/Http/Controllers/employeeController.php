<?php

namespace App\Http\Controllers;

use App\Exports\DivisionExport;
use App\Exports\EmployeeExport;
use App\Http\Requests\profileRequest;
use App\Models\Position;
use App\Models\division;
use App\Models\employee;
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
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Illuminate\Contracts\Session\Session;
use PDF;
use Psy\Readline\Hoa\Console;

class employeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employee = employee::paginate(5);
        return view('employee.index',compact('employee'));
    }

    public function create()
    {
        $divisi = division::all();
       
        return view('employee.create',compact('divisi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $input = $request->all();

            if ($image = $request->file('img_profile')) {
                $destinationPath = 'images/';
                $profileImage = $image->getClientOriginalName();
                $image->storeAs($destinationPath, $profileImage);
                $input['img_profile'] = $profileImage;
            }
            $user = User::create([
                'name' => $request->username,
                'email' => $request->email,
                'password' => $request->password,
                'role' => 'employees'
            ]);

            employee::create([
                'firstname' => $input['firstname'],
                'lastname' => $input['lastname'],
                'user_id' => $user->id,
                'staff_id' => $input['staff_id'],
                'img_profile' => $input['img_profile'],
                'division_id' => $input['division'],
                'gender' => $input['gender'],
                'address' => $input['address'],
                'date_of_birth' => $input['date_of_birth'],
                'status' => 'inActive'
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
        // $employee = employee::findOrFail($id);
        // $divisi = division::all();

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $employee = employee::findOrFail($id);
        $divisi = division::all();
        return view('employee.edit',compact('employee','divisi'));
    }

    public function update(Request $request, $id)
    {
        try {
            $employee = employee::findOrFail($id);
            $old_image = $employee->img_profile;

            $input = $request->all();

            if ($image = $request->file('img_profile')) {
                $destinationPath = 'images/';
                $profileImage = $image->getClientOriginalName();
                $image->storeAs($destinationPath, $profileImage);
                $input['img_profile'] = $profileImage;

                if ($old_image) {
                    Storage::delete('images/' . $old_image);
                }
            } else {
                $input['img_profile'] = $old_image;
            }

            $employee->update([
                'firstname' => $input['firstname'],
                'lastname' => $input['lastname'],
                'staff_id' => $input['staff_id'],
                'img_profile' => $input['img_profile'],
                'division_id' => $input['division'],
                'gender' => $input['gender'],
                'address' => $input['address'],
                'date_of_birth' => $input['date_of_birth']
            ]);

            $employee_name = $employee->firstname;

            return redirect()->route('employee')->with(['updated' => "$employee_name added successfully"]);
        } catch (\Throwable $th) {
            return redirect()->route('employee')->with(['error' => "Failed to update employee"]);
        }
    }

    public function destroy(string $id)
    {
        try {
            $employee = employee::findOrFail($id);
            if ($employee->img_profile) {
                $imagePath = public_path('images/') . $employee->img_profile;
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
        $employee = employee::withTrashed()->onlyTrashed()->get();
        $user = User::withTrashed()->onlyTrashed()->get();
        return view('employee.trash',compact('employee','user'));
    }

    public function export_excel()
    {
        return Excel::download(new EmployeeExport, 'Data-Employee.xlsx');
    }

    public function export_pdf()
    {
        $employee = employee::all();
        $division = division::all();

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

}
