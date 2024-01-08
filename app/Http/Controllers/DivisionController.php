<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Http\Request;
use App\Exports\DivisionExport;
use App\Imports\DivisionImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Contracts\Session\Session;

class DivisionController extends Controller
{
    /**
     * Display a listing of the resource.
     */

public function detailDivisi(Request $request)
     {
         $divisionId = $request->id;
         $perPage = 5; // Number of items per page

         // Get the requested page from the AJAX request
         $page = $request->input('page', 1);

         $positions = Position::where('division_id', $divisionId)->paginate($perPage, ['*'], 'page', $page);

         $positionData = [];

         foreach ($positions as $position) {
             $jumlah_pegawai = Employee::where('position_id', $position->id)->count();

             // Menambahkan data posisi dan jumlah pegawai ke dalam array
             $positionData[] = [
                 'position' => $position,
                 'positionCount' => $jumlah_pegawai,
             ];
         }

         return response()->json([
             'positionData' => $positionData,
             'currentPage' => $positions->currentPage(),
             'lastPage' => $positions->lastPage(),
         ]);
     }



    public function index(Request $request)
    {
        $divisi = Division::all();

        foreach ($divisi as $item) {
            $jumlah_posisi = Position::where('division_id', $item->id)->count();
            $item->jumlah_posisi = $jumlah_posisi;
        }

        return view('divisi.index',compact('divisi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('divisi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            Division::create([
                'name' => $request->divisi
            ]);
            $nama_divisi = $request->divisi;
            // return redirect()->back()->with(['success' => 'Divisi Baru telah ditambahkan']);
            return redirect()->back()->with(['success' => "$nama_divisi added successfully"]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['error' => "Failed to add division"]);
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

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $division = Division::findOrFail($id);
            $division->update([
                'name' => $request->divisi
            ]);
            $name_division = $request->divisi;

            return redirect()->route('divisi')->with(['success' => "$name_division updated successfully"]);
        } catch (\Throwable $th) {
            return redirect()->route('divisi')->with(['error' => "Failed to update division"]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        try {
            $divisi = Division::findOrFail($id);
            $posisi = Position::where('division_id', $divisi->id)->get();
            $inputName= $request->input('validName');

            foreach ($posisi as $posisiItem) {
                if ($posisiItem->employee->count() > 0) {
                    return redirect()->back()->with(['error' => 'Cannot delete division with associated employees']);
                }
                $posisiItem->delete();
            }

         if($inputName === $divisi->name){
             $nama_divisi = $divisi->division;
             $divisi->delete();
             return redirect()->back()->with(['delete' => "$nama_divisi deleted successfully"]);
         }else {
            return redirect()->back()->with(['error' => 'Wrong username']);
         };

        } catch (\Throwable $th) {
            return redirect()->back()->with(['error' => "Failed to delete division"]);
        }

    }

    public function export_excel()
    {

        return Excel::download(new DivisionExport, 'Data-Division.xlsx');

    }

    public function downloadTemplate()
    {
        $file_path = public_path("import/Template-Division.xlsx");

        if (file_exists($file_path)) {
            return response()->download($file_path);
        } else {
            return redirect('/division')->with('error', 'File not found.');
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

            Excel::import(new DivisionImport, public_path('storage/import/' . $nama_file));

            return redirect('/division')->with('success', 'Data imported successfully');
        } catch (\Throwable $th) {
            return redirect('/division')->with('error', 'Make sure there is no duplicate data');
        }
    }

    public function search(Request $request)
    {
        if($request->ajax())
        {
            $output="";
            $products=DB::table('products')->where('title','LIKE','%'.$request->search."%")->get();
            if($products)
            {
                foreach ($products as $key => $product) {
                $output.='<tr>'.
                '<td>'.$product->id.'</td>'.
                '<td>'.$product->title.'</td>'.
                '<td>'.$product->description.'</td>'.
                '<td>'.$product->price.'</td>'.
                '</tr>';
            }
                return Response($output);
            }
        }
    }

}
