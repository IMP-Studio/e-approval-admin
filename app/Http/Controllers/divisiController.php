<?php

namespace App\Http\Controllers;

use App\Models\division;
use App\Models\employee;
use App\Models\Position;
use Illuminate\Http\Request;
use App\Exports\DivisionExport;
use App\Imports\DivisionImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Contracts\Session\Session;

class divisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $divisi = division::paginate(5);
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
            division::create([
                'division' => $request->divisi
            ]);
            $nama_divisi = $request->divisi;
            // return redirect()->back()->with(['success' => 'Divisi Baru telah ditambahkan']);
            return redirect()->back()->with(['delete' => "$nama_divisi added successfully"]);
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
            $division = division::findOrFail($id);
            $division->update([
                'division' => $request->divisi
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
    public function destroy(string $id)
    {
        try {
            $divisi = Division::findOrFail($id);
            $posisi = Posisi::where('division_id', $divisi->id)->get();

            foreach ($posisi as $posisiItem) {
                if ($posisiItem->employee->count() > 0) {
                    return redirect()->back()->with(['error' => 'Cannot delete division with associated employees']);
                }
                $posisiItem->delete();
            }

            $nama_divisi = $divisi->division;
            $divisi->delete();

            return redirect()->back()->with(['delete' => "$nama_divisi deleted successfully"]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['error' => "Failed to delete division"]);
        }

    }

    public function export_excel()
    {

        return Excel::download(new DivisionExport, 'Data-Division.xlsx');

    }

    public function import_excel(Request $request)
    {
        $this->validate($request, [
            'import_file' => 'required|mimes:csv,xls,xlsx'
        ]);

        $file = $request->file('import_file');

        $nama_file = $file->getClientOriginalName();

        $file->move('export',$nama_file);

        Excel::import(new DivisionImport, public_path('export/'.$nama_file));

        return redirect('/divisi');
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
