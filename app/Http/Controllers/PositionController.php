<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Employee;
use Carbon\Carbon;
use App\Models\Position;
use Illuminate\Http\Request;

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
                        <div class="flex justify-center items-center">
                            <a class="flex items-center text-warning mr-3 edit-modal-search-class" data-Positionid="'. $item->id .'" data-PositionName="'. $item->name .'" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-edit-position-search">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="check-square" data-lucide="check-square" class="lucide lucide-check-square w-4 h-4 mr-1"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg> Edit
                            </a>
                            <a class="flex items-center text-danger delete-modal-search" data-id="'.  $item->id  .'" data-name="'. $item->name .'" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal-search">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="check-square" data-lucide="check-square" class="lucide lucide-check-square w-4 h-4 mr-1"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg> Delete
                            </a>
                        </div>
                    </td>
                </tr>';
            }

        return response($output);
        }

        $divisi = Division::all();
        return view('posisi.index',compact('posisi','divisi'));
    }

    public function detailPosition(Request $request)
    {
        $positions = Employee::where('position_id', $request->id) ->with('division')->get();
    
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
            $inputName= $request->input('validNamePosisi');
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
}
