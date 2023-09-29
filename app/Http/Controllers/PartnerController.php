<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\Project;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index()
    {
        $partner = Partner::paginate(5);

        foreach ($partner as $item) {
            $project = Project::where('partner_id', $item->id)->count();
            $item->jumlah_project = $project;
        }

        return view('partner.index',compact('partner'));
    }

    public function store(Request $request)
    {
        try {
            Partner::create([
                'name' => $request->partner
            ]);
            $nama_partner = $request->partner;
            // return redirect()->back()->with(['success' => 'Divisi Baru telah ditambahkan']);
            return redirect()->back()->with(['success' => "$nama_partner added successfully"]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['error' => "Failed to add partner"]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $partner = Partner::findOrFail($id);
            $partner->update([
                'name' => $request->partner
            ]);
            $name_partner = $request->partner;

            return redirect()->route('partner')->with(['success' => "$name_partner updated successfully"]);
        } catch (\Throwable $th) {
            return redirect()->route('partner')->with(['error' => "Failed to update division"]);
        }
    }

    public function destroy(Request $request, string $id)
    {
        try {
            $partner = Partner::findOrFail($id);
            $projects = Project::where('partner_id', $partner->id)->get();
            $inputName= $request->input('validName');
            
            foreach ($projects as $projectItem) {
                if ($projectItem->end_date > now()) {
                    return redirect()->back()->with(['error' => 'Cannot delete partner when project is not yet ended']);
                }
            }
            
            // if ($projects->count() > 0) {
            //     return redirect()->back()->with(['error' => 'Cannot delete partner with associated project']);
            // }
            
            
        if($inputName === $partner->name){            
               $nama_partner = $partner->name;
               $partner->delete();
               $projectItem->delete();
            return redirect()->back()->with(['delete' => "$nama_partner deleted successfully"]);
         }else {
            return redirect()->back()->with(['error' => 'Wrong username']);
         };

        } catch (\Throwable $th) {
            return redirect()->back()->with(['error' => "Failed to delete Partner"]);
        }
    }
}
