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
            $employee = Employee::where('first_name','LIKE', '%' . $query . '%')->paginate(5);

            $output = '';
            $iteration = 0;

            foreach ($employee as $item) {
                $iteration++;
                $output .= '<tr class="intro-x h-16" id="data-search">
                    <td class="w-4 text-center">' . $iteration . '.</td>
                    <td class="flex justify-center align-center">
                        <div class="w-12 h-12 image-fit zoom-in">';

                if ($item->avatar) {
                    $output .= '<img data-action="zoom" class="tooltip rounded-full" src="' . asset('storage/' . $item->avatar) . '" title="Uploaded at ' . ($item->updated_at ? $item->updated_at->format('d M Y') : '?') . '">';
                } elseif ($item->gender == 'male') {
                    $output .= '<img data-action="zoom" class="tooltip rounded-full" src="' . asset('images/default-boy.jpg') . '" title="Uploaded at ' . ($item->updated_at ? $item->updated_at->format('d M Y') : '?') . '">';
                } elseif ($item->gender == 'female') {
                    $output .= '<img data-action="zoom" class="tooltip rounded-full" src="' . asset('images/default-women.jpg') . '" title="Uploaded at ' . ($item->updated_at ? $item->updated_at->format('d M Y') : '?') . '">';
                }

                $output .= '</div>
                    </td>
                    <td class="w-50 text-center">' . $item->user->name . '</td>
                    <td class="text-center">' . $item->id_number . '</td>
                    <td class="text-center capitalize">' . ($item->position ? $item->position->name : '-') . '</td>
                    <td class="w-40">
                        <div class="flex items-center justify-center">';

                if ($item->gender === 'male') {
                    $output .= '<div class="text-success flex">
                        <i data-lucide="user" class="w-4 h-4 mr-2"></i> ' . $item->gender . '
                    </div>';
                } else {
                    $output .= '<div class="text-warning flex">
                        <i data-lucide="user" class="w-4 h-4 mr-2"></i> ' . $item->gender . '
                    </div>';
                }

                $output .= '</div>
                    </td>
                    <td class="table-report__action w-56">
                        <div class="flex justify-center items-center">';
                        if (auth()->user()->can('edit_employees')) {
                            $output .=
                            '<a class="flex items-center text-success mr-3" href="' . route('employee.edit', $item->id) . '">'.
                            '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="check-square" data-lucide="check-square" class="lucide lucide-check-square w-4 h-4 mr-1"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg> Edit '.
                            '</a>';
                        }

                        $output .=
                            '<a class="flex items-center text-warning delete-button mr-3 show-modal-search" data-email="'. $item->user->email .'" data-name="'. $item->user->name .'" data-avatar="'. $item->avatar .'" data-gender="'. $item->gender .'" data-firstname="'. $item->first_name .'" data-LastName="'. $item->last_name .'" data-stafId="'. $item->id_number .'" data-Divisi="'. $item->division->name .'" data-Posisi="'.$item->position->name .'" data-Address="'. $item->address .'" data-BirthDate="'. $item->birth_date .'" href="javascript:;" data-tw-toggle="modal" data-tw-target="#show-modal-search">'.
                                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="eye" data-lucide="eye" class="lucide lucide-eye w-4 h-4 mr-1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> Show '.
                            '</a>';

                        if (auth()->user()->can('delete_employees')) {
                        $output .=
                            '<a class="flex items-center text-danger delete-modal-search" data-id="'.  $item->id  .'" data-name="'. $item->first_name .' '. $item->last_name .'" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal-search">'.
                                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="check-square" data-lucide="check-square" class="lucide lucide-check-square w-4 h-4 mr-1"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg> Delete '.
                            '</a>';
                        }
                        $output .= '</div>';
                    '</td>'.
               '</tr>';
            }

            return response($output);

        }else {
            $employee = Employee::paginate(5);
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
                $profileImage = $image->getClientOriginalName();
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

            $input = $request->all();

            if ($image = $request->file('avatar')) {
                $destinationPath = 'storage/';
                $profileImage = $image->getClientOriginalName();
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
                'position_id' => $input['position'],
                'gender' => $input['gender'],
                'address' => $input['address'],
                'birth_date' => $birthDate
            ]);
            $user = $employee->user;
            $user->update([
                'name' => $input['first_name'] . ' ' . $input['last_name']
            ]);

            $employee_name = $user->name;

            return redirect()->route('employee')->with(['updated' => "$employee_name updated successfully"]);
        } catch (\Throwable $th) {
            return redirect()->route('employee')->with(['error' => "Failed to update employee"]);
        }
    }

    public function destroy(string $id, Request $request)
    {
        try {
            $employee = Employee::findOrFail($id);

            $user = User::where('id', $employee->user_id)->first();
            $inputName= $request->input('validNameEmployee');

            $employee_name = $user->name;

            if ($user && $inputName === $user->name) {
                if ($employee->avatar) {
                    $imagePath = public_path('storage/') . $employee->avatar;
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                $user->delete();
                $employee->delete();
                return redirect()->back()->with(['delete' => "$employee_name deleted successfully"]);
            }else{
                return redirect()->back()->with(['error' => 'Wrong username']);
            }


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

            $file->move('storage/export', $nama_file);

            Excel::import(new EmployeeImport, public_path('storage/export/' . $nama_file));

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
