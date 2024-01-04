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
        $posisi = Position::all();
        foreach ($posisi as $item) {
            $jumlah_pegawai = Employee::where('position_id', $item->id)->count();
            $item->jumlah_pegawai = $jumlah_pegawai;
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

            Employee::where('position_id', $posisi->id)
            ->update(['division_id' => $request->division_id]);
            
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

            $file->move('storage/import', $nama_file);

            Excel::import(new PositionImport, public_path('storage/import/' . $nama_file));

            return redirect('/position')->with('success', 'Data imported successfully');
        } catch (\Throwable $th) {
            return redirect('/position')->with('error', 'Make sure there is no duplicate data');
        }
    }
}
