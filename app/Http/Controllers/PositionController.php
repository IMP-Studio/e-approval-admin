<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\division;
use App\Models\employee;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posisi = Position::paginate(5);
        foreach ($posisi as $item) {
            $jumlah_pegawai = employee::where('position_id', $item->id)->count();
            $item->jumlah_pegawai = $jumlah_pegawai;
        }
        $divisi = division::all();
        return view('posisi.index',compact('posisi','divisi'));
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
    public function destroy(string $id)
    {
        try {
            $posisi = Position::findOrFail($id);
            if ($posisi->employee->count() > 0) {
                return redirect()->back()->with(['error' => 'Cannot delete position with associated employees']);
            }
            $posisi->delete();
            $nama_posisi = $posisi->name;

            return redirect()->back()->with(['delete' => "$nama_posisi deleted successfully"]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['error' => "Failed to delete position"]);
            //throw $th;
        }
    }
}
