<?php

namespace App\Http\Controllers;

use App\Imports\PartnerImport;
use App\Models\Partner;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Exports\PartnerExport;
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
        $partner = Partner::all();
    
        foreach ($partner as $item) {
            $project = Project::where('partner_id', $item->id)->count();
            $item->jumlah_project = $project;
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

    public function export_excel()
    {
        return Excel::download(new PartnerExport, 'Data-Partner.xlsx');
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
