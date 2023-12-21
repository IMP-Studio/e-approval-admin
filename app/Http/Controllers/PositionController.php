<?php

namespace App\Http\Controllers;

use App\Imports\PositionImport;
use App\Models\Division;
use App\Models\Employee;
use Carbon\Carbon;
use App\Models\Position;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $posisi = Position::paginate(5);
        foreach ($posisi as $item) {
            $jumlah_pegawai = Employee::where('position_id', $item->id)->count();
            $item->jumlah_pegawai = $jumlah_pegawai;
        }

        if ($request->ajax()) {
            $query = $request->input('query');
            $posisi = Position::where('name','LIKE', '%' . $query . '%')->paginate(5);

            foreach ($posisi as $item) {
                $jumlah_pegawai = Employee::where('position_id', $item->id)->count();
                $item->jumlah_pegawai = $jumlah_pegawai;
            }


            $output = '';
            $iteration = 0;
            $divisi = Division::all();


            foreach ($posisi as $item) {
                $iteration++;
                    $output .= '<tr class="intro-x h-16">
                        <td class="w-4 text-center">' . $iteration . '.</td>
                        <td class="w-50 text-center capitalize">' . $item->name . '</td>
                        <td class="w-50 text-center capitalize">' . $item->jumlah_pegawai . ' Pegawai</td>
                        <td class="table-report__action w-56">
                            <div class="flex justify-center items-center">';
                                if (auth()->check() && auth()->user()->can('edit_positions')) {
                                   $output .='<a class="flex items-center text-success mr-3 edit-modal-search-class" data-Positionid="'. $item->id .'" data-PositionName="'. $item->name .'" data-DivisionId="'. $item->division_id .'" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-edit-position-search">'.
                                                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="check-square" data-lucide="check-square" class="lucide lucide-check-square w-4 h-4 mr-1"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg> Edit'.
                                             '</a>';
                                }

                                $output .= '<a data-positionName="'. $item->name .'" data-presenceId="'. $item->id .'" data-DivisionId="'. $item->division_id .'" class="mr-3 flex items-center text-warning detail-presence-modal-search" href="javascript:;" data-tw-toggle="modal" data-tw-target="#detail-division-modal">'.
                                                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="eye" data-lucide="eye" class="lucide lucide-eye w-4 h-4 mr-1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> Detail '.
                                            '</a>';

                                if (auth()->check() && auth()->user()->can('delete_positions')) {
                                $output .='<a class="flex items-center text-danger delete-modal-search" data-id="'.  $item->id  .'" data-name="'. $item->name .'" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal-search">'.
                                                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="check-square" data-lucide="check-square" class="lucide lucide-check-square w-4 h-4 mr-1"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg> Delete'.
                                          '</a>';
                                }
                            $output .= '</div>';
                        '</td>'.
                    '</tr>';
                }

        return response($output);
        }

        $divisi = Division::all();
        return view('posisi.index',compact('posisi','divisi'));
    }

    public function detailPosition(Request $request)
    {
        $perPage = 5;
        $positions = Employee::where('position_id', $request->id)->with('division')->paginate($perPage);

        return response()->json(['positionData' => $positions]);
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
            Position::create([
                'division_id' => $request->division_id,
                'name' => $request->name,
                'created_at' => Carbon::now()->setTimezone('Asia/Jakarta'),
                'updated_at' => Carbon::now()->setTimezone('Asia/Jakarta'),
            ]);
            $nama_posisi = $request->name;
            return redirect()->back()->with(['success' => "$nama_posisi added successfully"]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['error' => "Failed to add position"]);
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
            $posisi = Position::findOrFail($id);
            $posisi->update([
                'division_id' => $request->division_id,
                'name' => $request->name,
            ]);
            $nama_posisi = $request->name;
            return redirect()->back()->with(['success' => "$nama_posisi updated successfully"]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['error' => "Failed to updated position"]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        try {
            $posisi = Position::findOrFail($id);
            $inputName= $request->input('validNamePosition');
            if ($posisi->employee->count() > 0) {
                return redirect()->back()->with(['error' => 'Cannot delete position with associated employees']);
            }

            if ($inputName === $posisi->name) {
                $posisi->delete();
                $nama_posisi = $posisi->name;
                return redirect()->back()->with(['delete' => "$nama_posisi deleted successfully"]);
            }else {
               return redirect()->back()->with(['error' => 'Wrong username']);
            };

        } catch (\Throwable $th) {
            return redirect()->back()->with(['error' => "Failed to delete position"]);
            //throw $th;
        }
    }

    public function downloadTemplate()
    {
        $file_path = public_path("import/Template-Position.xlsx");

        if (file_exists($file_path)) {
            return response()->download($file_path);
        } else {
            return redirect('/position')->with('error', 'File not found.');
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

            Excel::import(new PositionImport, public_path('storage/export/' . $nama_file));

            return redirect('/position')->with('success', 'Data imported successfully');
        } catch (\Throwable $th) {
            return redirect('/position')->with('error', 'Make sure there is no duplicate data');
        }
    }
}
