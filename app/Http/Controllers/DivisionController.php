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
        $divisi = Division::paginate(5);

        foreach ($divisi as $item) {
            $jumlah_posisi = Position::where('division_id', $item->id)->count();
            $item->jumlah_posisi = $jumlah_posisi;
        }

        if ($request->ajax()) {
            $query = $request->input('query');
            $divisi = Division::where('name', 'LIKE', '%' . $query . '%')->paginate(5);
            foreach ($divisi as $item) {
                $jumlah_posisi = Position::where('division_id', $item->id)->count();
                $item->jumlah_posisi = $jumlah_posisi;
            }


        $output = '';
        $iteration = 0;
        $caneditD = 'edit_divisions';

        foreach ($divisi as $item) {
        $iteration++;
            $output .= '<tr class="intro-x h-16">
                <td class="w-4 text-center">' . $iteration . '.</td>
                <td class="w-50 text-center capitalize">' . $item->name . '</td>
                <td class="w-50 text-center capitalize">' . $item->jumlah_posisi . ' Posisi</td>
                <td class="table-report__action w-56">
                    <div class="flex justify-center items-center">';
                    if (auth()->user()->can('edit_divisions')) {
                    $output .= '<a class="flex items-center text-success mr-3 edit-modal-divisi-search-class" data-Divisiid="'. $item->id .'" data-DivisiName="'. $item->name .'" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-edit-divisi-search">'.
                        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="check-square" data-lucide="check-square" class="lucide lucide-check-square w-4 h-4 mr-1"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg> Edit'.
                    '</a>';
                    }

                    $output .= '<a data-divisionId="'. $item->id .'" class="mr-3 flex items-center text-warning detail-division-modal-search" href="javascript:;" data-tw-toggle="modal" data-tw-target="#detail-division-modal">'.
                        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="eye" data-lucide="eye" class="lucide lucide-eye w-4 h-4 mr-1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> Detail'.
                    '</a>';

                    if (auth()->user()->can('delete_divisions')) {
                    $output .= '<a class="flex items-center text-danger delete-divisi-modal-search" data-DeleteDivisiId="'. $item->id .'" data-DeleteDivisiName="'. $item->name .'" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal-search">'.
                        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="check-square" data-lucide="check-square" class="lucide lucide-check-square w-4 h-4 mr-1"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg> Delete'.
                    '</a>';
                    }

                    $output .= '</div>';
                '</td>'.
            '</tr>';
        }

        return response($output);

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
