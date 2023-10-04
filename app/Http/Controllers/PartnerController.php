<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\Project;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $partner = Partner::paginate(5);
    
        foreach ($partner as $item) {
            $project = Project::where('partner_id', $item->id)->count();
            $item->jumlah_project = $project;
        }
    
        if ($request->ajax()) {
            $query = $request->input('query');
            $partner = Partner::where('name', 'LIKE', '%' . $query . '%')->paginate(5);

            foreach ($partner as $item) {
                $project = Project::where('partner_id', $item->id)->count();
                $item->jumlah_project = $project;
            }
    
            $output = '';
            $iteration = 0;
    
            foreach ($partner as $item) {
                $iteration++;
                $output .= '
                <tr class="intro-x h-16">
                    <td class="w-4 text-center">
                        '. $iteration .'
                    </td>
                    <td class="w-50 text-center capitalize">
                        '. $item->name .'                </td>
                    <td class="w-50 text-center capitalize">
                        '. $item->jumlah_project .'                </td>
                    <td class="table-report__action w-56">
                        <div class="flex justify-center items-center">
                            <a class="flex items-center text-warning mr-3" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-edit-divisi">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="check-square" data-lucide="check-square" class="lucide lucide-check-square w-4 h-4 mr-1"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg> Edit
                            </a>
                            
                            <a data-partnerId="'. $item->id .'" class="mr-3 flex items-center text-success detail-partner-modal-search" href="javascript:;" data-tw-toggle="modal" data-tw-target="#detail-partner-modal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="eye" data-lucide="eye" class="lucide lucide-eye w-4 h-4 mr-1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> Detail
                            </a>
    
                            <a class="flex items-center text-danger delete-button" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal-'. $item->id .'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="check-square" data-lucide="check-square" class="lucide lucide-check-square w-4 h-4 mr-1"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg> Delete
                            </a>
                        </div>
                    </td>
                </tr>';
            }
            return response($output);
        }
    
        return view('partner.index', compact('partner'));
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
            $inputName = $request->input('validName');

            foreach ($projects as $projectItem) {
                if ($projectItem->end_date > now()) {
                    return redirect()->back()->with(['error' => 'Cannot delete partner when project is not yet ended']);
                }
            }

            // if ($projects->count() > 0) {
            //     return redirect()->back()->with(['error' => 'Cannot delete partner with associated project']);
            // }


            if ($inputName === $partner->name) {
                $nama_partner = $partner->name;
                $partner->delete();
                $projectItem->delete();
                return redirect()->back()->with(['delete' => "$nama_partner deleted successfully"]);
            } else {
                return redirect()->back()->with(['error' => 'Wrong username']);
            };
        } catch (\Throwable $th) {
            return redirect()->back()->with(['error' => "Failed to delete Partner"]);
        }
    }
}
