<?php

namespace App\Http\Controllers;

use App\Imports\PartnerImport;
use App\Models\Partner;
use App\Models\Project;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PartnerController extends Controller
{
    public function detailpartner(Request $request)
    {
        $positions = Project::where('partner_id', $request->id)->get();
        // dd($positions);

        return response()->json(['positionData' => $positions]);
    }

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
                        <div class="flex justify-center items-center">';
                            if (auth()->user()->can('edit_partners')) {
                            $output .=
                                '<a class="flex items-center text-success mr-3 edit-modal-partner-search-class" data-partnerName="'.$item->name.'" data-descId="'.$item->description.'" data-partnerId="'.$item->id.'" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-edit-partner">'.
                                    '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="check-square" data-lucide="check-square" class="lucide lucide-check-square w-4 h-4 mr-1"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg> Edit '.
                                '</a>';
                            }

                            $output .=
                            '<a  class="mr-3 flex items-center text-warning detail-partner-modal-search" data-partnerId="'. $item->id .'" data-partnerName="'. $item->name .'"  data-partnerDesc="'. $item->description .'" href="javascript:;" data-tw-toggle="modal" data-tw-target="#detail-partner-modal">'.
                                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="eye" data-lucide="eye" class="lucide lucide-eye w-4 h-4 mr-1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> Detail '.
                            '</a>';

                            if (auth()->user()->can('delete_partners')) {
                            $output .=
                            '<a class="flex items-center text-danger deletepartnermodal" data-partnerid="'. $item->id .'" data-partnername="'. $item->name .'" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-partner-modal-search">'.
                                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="check-square" data-lucide="check-square" class="lucide lucide-check-square w-4 h-4 mr-1"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg> Delete '.
                            '</a>';
                            }
                        $output .= '</div>';
                    '</td>'.
                '</tr>';
            }
            return response($output);
        }

        return view('partner.index', compact('partner'));
    }


    public function store(Request $request)
    {
        try {
            Partner::create([
                'name' => $request->partner,
                'description' => $request->description
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
                'name' => $request->name,
                'description' => $request->description
            ]);
            $name_partner = $request->name;

            return redirect()->route('partner')->with(['success' => "$name_partner updated successfully"]);
        } catch (\Throwable $th) {
            return redirect()->route('partner')->with(['error' => "Failed to update partner"]);
        }
    }

    public function destroy(Request $request, string $id)
    {
        try {
            $partner = Partner::findOrFail($id);
            $projects = Project::where('partner_id', $partner->id)->get();
            $inputName = $request->input('validNamePartner');

            foreach ($projects as $projectItem) {
                if ($projectItem->end_date > now()) {
                    return redirect()->back()->with(['error' => 'Cannot delete partner when project is not yet ended']);
                } else {
                    $projectItem->delete();
                }
            }




            if ($inputName === $partner->name) {
                $nama_partner = $partner->name;
                $partner->delete();
                return redirect()->back()->with(['success' => "$nama_partner deleted successfully"]);
            } else {
                return redirect()->back()->with(['error' => 'Wrong username']);
            };
        } catch (\Throwable $th) {
            return redirect()->back()->with(['error' => "Failed to delete Partner"]);
        }
    }

    /**
     * @return mixed
     */
    public function downloadTemplate()
    {
        $file_path = public_path("import/Template-Partner.xlsx");

        if (file_exists($file_path)) {
            return response()->download($file_path);
        } else {
            return redirect('/partner')->with('error', 'File not found.');
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importExcel(Request $request)
    {
        try {
            $this->validate($request, [
                'import_file' => 'required|mimes:csv,xls,xlsx'
            ]);

            $file = $request->file('import_file');

            // @phpstan-ignore-next-line
            $nama_file = rand() . $file->getClientOriginalName();

            // @phpstan-ignore-next-line
            $file->move('storage/import', $nama_file);

            Excel::import(new PartnerImport, public_path('storage/import/' . $nama_file));

            return redirect('/partner')->with('success', 'Data imported successfully');
        } catch (\Throwable $th) {
            return redirect('/partner')->with('error', 'Make sure there is no duplicate data');
        }
    }
}
