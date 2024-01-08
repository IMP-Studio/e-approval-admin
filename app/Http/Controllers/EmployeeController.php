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
    public function index(Request $request)
    {
        $query = $request->input('query');

        if ($request->ajax()) {
            $employee = Employee::all();

            $output = '';
            $iteration = 0;


        }else {
            $employee = Employee::all();
            return view('employee.index',compact('employee'));
        }


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


            if ($request->hasFile('avatar')) {
                $image = $request->file('avatar');
                $destinationPath = 'storage/';
                $profileImage = date('Ymdhis') . rand() . $image->getClientOriginalName();
                $image->storeAs($destinationPath, $profileImage);
                $input['avatar'] = $profileImage;
            } else {
                // Jika input 'avatar' tidak diisi, atur nilai 'avatar' menjadi null atau sesuai kebijakan Anda.
                $input['avatar'] = null;
            }

            $birthDate = Carbon::createFromFormat('d M, Y', $input['birth_date'])->format('Y-m-d');
            $user = User::create([
                'name' => $input['first_name'] . ' ' . $input['last_name'],
                'email' => $request->email,
                'password' => $request->password,
            ]);

            $user->givePermissionTo('can_access_mobile');


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
                'birth_date' => $birthDate,
                'is_active' => true
            ]);

            $user->assignRole('employee');


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

    public function getDataPosition(Request $request)
    {
        $divisionId = $request->input('division_id');
        $positionId = $request->input('position_id');
        $positions = Position::where('division_id', $divisionId)->get();

        // {{ $positions->id == $positionId ? 'selected' : '' }}

        // Kembalikan data posisi dalam bentuk HTML
        $options = '';
        foreach ($positions as $position) {
            $selected = ($position->id == $positionId) ? 'selected' : '';
            $options .= '<option value="' . $position->id . '" ' . $selected . '>' . $position->name . '</option>';
        }

        return response()->json(['options' => $options]);
    }

    public function edit(string $id)
    {
        $employee = Employee::findOrFail($id);
        $division = Division::all();
        $position = Position::where('division_id',$employee->division_id)->get();
        return view('employee.edit',compact('employee','position', 'division'));
    }

    public function update(Request $request, $id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $old_image = $employee->avatar;
            $oldPosition = $employee->position->name;
            $getPosition = $request->input('position');
            $input = $request->all();
            $user = $employee->user;
            
            $newPosition = Position::find($getPosition);

            try {
                if ($oldPosition == 'Human Resource Staff' && $newPosition->name != 'Human Resource Staff') {
                    $user->revokePermissionTo('approve_allowed');
                    $user->revokePermissionTo('view_request_preliminary');
                    $user->revokePermissionTo('can_access_web');
                    $user->revokePermissionTo('reject_presence');
                }

                if ($newPosition->name == 'Human Resource Staff' && $oldPosition != 'Human Resource Staff') {
                    $user->givePermissionTo('can_access_web');
                    $user->givePermissionTo('view_request_preliminary');
                    $user->givePermissionTo('approve_allowed');
                    $user->givePermissionTo('reject_presence');
                }
            } catch (\Throwable $th) {
                return redirect()->back()->with(['error' => 'Failed to update employee because permission not updated']);
            }
            
            if ($image = $request->file('avatar')) {
                $destinationPath = 'storage/';
                $profileImage = date('Ymdhis') . rand() . $image->getClientOriginalName();
                $image->storeAs($destinationPath, $profileImage);
                $input['avatar'] = $profileImage;

                if ($old_image) {
                    Storage::delete('storage/' . $old_image);
                }
            } else {
                $input['avatar'] = $old_image;
            }
            $birthDate = Carbon::createFromFormat('d M, Y', $input['birth_date'])->format('Y-m-d');
            $employee->update([
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'id_number' => $input['id_number'],
                'avatar' => $input['avatar'],
                'division_id' => $input['division'],
                'position_id' => $input['position'],
                'gender' => $input['gender'],
                'address' => $input['address'],
                'birth_date' => $birthDate
            ]);
            $user->update([
                'name' => $input['first_name'] . ' ' . $input['last_name']
            ]);


            $employee_name = $user->name;

            return redirect()->route('employee')->with(['updated' => "$employee_name updated successfully"]);
        } catch (\Throwable $th) {
            return redirect()->route('employee')->with(['error' => "Failed to update employee"]);
        }
    }

    /**
     * Menghapus data secara soft delete
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id, Request $request)
    {
        try {
            DB::beginTransaction();
            $employee = Employee::findOrFail($id);

            $user = User::where('id', $employee->user_id)->first();
            $inputName= $request->input('validNameEmployee');

            $employee_name = $user->name;

            if ($user && $inputName === $user->name) {
                $employee->delete();
                $user->delete();
                DB::commit();
                return redirect()->back()->with(['delete' => "$employee_name deleted successfully"]);
            } else {
                return redirect()->back()->with(['error' => 'Wrong username']);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with(['error' => "Failed to delete employee"]);
        }
    }

    /**
     * Menampilkan data pegawail yang sudah dihapus
     *
     * @return \Illuminate\View\View
     */
    public function trash()
    {
        $employee = Employee::withTrashed()->onlyTrashed()->get();
        return view('employee.trash',compact('employee'));
    }

    /**
     * Mengembalikan data yang sudah di delete
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id, Request $request)
    {
        try {
            DB::beginTransaction();
            $employee = Employee::onlyTrashed()->where('id', $id)->first();

            $inputName = $request->validNameEmployeeRes;
            // @phpstan-ignore-next-line
            if ($employee && $inputName === $employee->user->name) {
                $employee->restore();
                $employee->user()->restore();
                DB::commit();
                return redirect()->back()->with(['success' => "Restore successfully"]);
            } else {
                DB::rollBack();
                return redirect()->back()->with(['error' => "Wrong Username"]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with(['error' => "Restore failed"]);
        }
    }

    /**
     * Menghapus data secara permanen
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyPermanently($id, Request $request)
    {
        try {
            DB::beginTransaction();
            $employee = Employee::onlyTrashed()->where('id', $id)->first();
            $user = User::withTrashed()->where('id', $employee->user_id)->first();

            $inputName = $request->validNameEmployeeDel;
            if ($user && $inputName === $user->name) {
                $user->standups()->forceDelete();
                $user->telework()->forceDelete();
                $user->workTrip()->forceDelete();
                $user->leave()->forceDelete();
                $user->subtituteLeave()->forceDelete();
                $user->presence()->forceDelete();
                $user->otpVerification()->forceDelete();
                $user->employee()->forceDelete();
                $user->forceDelete();
                DB::commit();
                return redirect()->back()->with(['delete' => "$inputName deleted successfully"]);
            } else {
                DB::rollBack();
                return redirect()->back()->with(['error' => "Wrong Username"]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with(['error' => "Delete failed. An error occurred."]);
        }
    }

    public function export_excel()
    {
        return Excel::download(new EmployeeExport, 'Data-Employee.xlsx');
    }

    public function downloadTemplate()
    {
        $file_path = public_path("import/Template-Employee.xlsx");

        if (file_exists($file_path)) {
            return response()->download($file_path);
        } else {
            return redirect('/employee')->with('error', 'File not found.');
        }
    }

    public function importExcel(Request $request)
    {
        try {
            $this->validate($request, [
                'import_file' => 'required|mimes:csv,xls,xlsx'
            ]);

            $file = $request->file('import_file');

            $nama_file = rand() . $file->getClientOriginalName();

            $file->move('storage/import', $nama_file);

            Excel::import(new EmployeeImport, public_path('storage/import/' . $nama_file));

            return redirect('/employee')->with('success', 'Data imported successfully');
        } catch (\Throwable $th) {
            return redirect('/employee')->with('error', 'Make sure there is no duplicate data');
        }
    }

    public function getPositions($divisionId)
    {
        $positions = Position::where('division_id', $divisionId)->get();

        return response()->json(['data' => $positions]);
    }

}
