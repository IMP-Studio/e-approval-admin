<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Employee;
use App\Models\Presence;
use App\Models\StatusCommit;
use App\Models\User;
use App\Models\WorkTrip;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ApproveController extends Controller
{
    // ------------------------------------------------------- Head of Tired --------------------------------------------------------------- \\

    // worktripHt
    public function workTripHt(Request $request)
    {
        $loggedInUser = auth()->user();

        $kepaladivisi = null;
        if ($loggedInUser->employee) {
            $kepaladivisi = $loggedInUser->employee->division_id;
        }


        // Only fetch WorkTrip data
        $workTripDataQuery = Presence::with('worktrip.statusCommit')
            ->where('category', 'work_trip');

        // Condition to check if the user doesn't have the 'super-admin' role
        if (!$loggedInUser->hasRole('super-admin') && $kepaladivisi) {
            $workTripDataQuery->whereHas('user.employee', function ($employeeQuery) use ($kepaladivisi) {
                $employeeQuery->where('division_id', $kepaladivisi);
            });
        }

        $workTripData = $workTripDataQuery->whereHas('worktrip', function ($worktripQuery) {
            $today = Carbon::today('Asia/Jakarta');
            $worktripQuery->whereHas('statusCommit', function ($statusCommitQuery) {
                $statusCommitQuery->where('status', 'pending');
            })
                ->whereDate('start_date', '<=', $today) // Add condition for start_date
                ->whereDate('end_date', '>=', $today);
        })
            ->orderBy('entry_time', 'asc')
            ->paginate(10);

        // ->map(function ($item) {
        //     $item->category = 'work_trip';
        //     return $item;
        // })

        return view('approve.headTired.worktrip.index', compact('workTripData'));
    }

    // approve Ht
    public function approveWkHt(Request $request, $id)
    {
        $loggedInUser = auth()->user();
        $statusCommit = StatusCommit::find($id);

        if (!$statusCommit) {
            return back()->with('error', 'StatusCommit not found.');
        }

        // Ubah nilai status menjadi "preliminary"
        $statusCommit->update([
            'approver_id' => $loggedInUser->id,
            'status' => 'preliminary',
        ]);

        $message = $request->message;

        return redirect()->route('approveht.worktripHt')->with(['success' => "$message approved successfully"]);
    }

    // Reject Ht
    public function rejectWkHt(Request $request, $id)
    {
        $loggedInUser = auth()->user();
        $statusCommit = StatusCommit::find($id);

        if (!$statusCommit) {
            return back()->with('error', 'StatusCommit not found.');
        }

        // Ubah nilai status menjadi "preliminary"
        $statusCommit->update([
            'approver_id' => $loggedInUser->id,
            'status' => 'rejected',
            'description' => $request->description,
        ]);

        $message = $request->message;

        return redirect()->route('approveht.worktripHt')->with(['success' => "$message rejected successfully"]);
    }
    // worktripHt end \\

    // telework Ht
    public function teleworkHt()
    {
        $loggedInUser = auth()->user();

        $kepaladivisi = null;
        if ($loggedInUser->employee) {
            $kepaladivisi = $loggedInUser->employee->division_id;
        }


        // Only fetch WorkTrip data
        $teleworkDataQuery = Presence::with('telework.statusCommit')
            ->where('category', 'telework');

        // Condition to check if the user doesn't have the 'super-admin' role
        if (!$loggedInUser->hasRole('super-admin') && $kepaladivisi) {
            $teleworkDataQuery->whereHas('user.employee', function ($employeeQuery) use ($kepaladivisi) {
                $employeeQuery->where('division_id', $kepaladivisi);
            });
        }

        $teleworkData = $teleworkDataQuery->whereHas('telework', function ($teleworkQuery) {
            $teleworkQuery->whereHas('statusCommit', function ($statusCommitQuery) {
                $statusCommitQuery->where('status', 'pending');
            });
        })
            ->orderBy('entry_time', 'asc')
            ->paginate(10);


        return view('approve.headTired.telework.index', compact('teleworkData'));
    }

    // approve Telework
    public function approveTeleHt(Request $request, $id)
    {
        $loggedInUser = auth()->user();
        $statusCommit = StatusCommit::find($id);

        if (!$statusCommit) {
            return back()->with('error', 'StatusCommit not found.');
        }

        // Ubah nilai status menjadi "preliminary"
        $statusCommit->update([
            'approver_id' => $loggedInUser->id,
            'status' => 'preliminary',
        ]);

        $message = $request->message;

        return redirect()->route('approveht.teleworkHt')->with(['success' => "$message approved successfully"]);
    }

    // Reject Telework
    public function rejectTeleHt(Request $request, $id)
    {
        $loggedInUser = auth()->user();
        $statusCommit = StatusCommit::find($id);

        if (!$statusCommit) {
            return back()->with('error', 'StatusCommit not found.');
        }

        // Ubah nilai status menjadi "preliminary"
        $statusCommit->update([
            'approver_id' => $loggedInUser->id,
            'status' => 'rejected',
            'description' => $request->description,
        ]);

        $message = $request->message;

        return redirect()->route('approveht.teleworkHt')->with(['success' => "$message rejected successfully"]);
    }
    // TeleWork Ht

    // Leave HT  
    public function leaveHt()
    {
        $loggedInUser = auth()->user();

        $kepaladivisi = null;
        if ($loggedInUser->employee) {
            $kepaladivisi = $loggedInUser->employee->division_id;
        }


        // Only fetch WorkTrip data
        $leaveDataQuery = Presence::with('leave.statusCommit')
            ->where('category', 'leave');

        // Condition to check if the user doesn't have the 'super-admin' role
        if (!$loggedInUser->hasRole('super-admin') && $kepaladivisi) {
            $leaveDataQuery->whereHas('user.employee', function ($employeeQuery) use ($kepaladivisi) {
                $employeeQuery->where('division_id', $kepaladivisi);
            });
        }

        $leavekData = $leaveDataQuery->whereHas('leave', function ($leaveQuery) {
            $leaveQuery->whereHas('statusCommit', function ($statusCommitQuery) {
                $statusCommitQuery->where('status', 'pending');
            });
        })
            ->orderBy('entry_time', 'asc')
            ->paginate(10);

        return view('approve.headTired.leave.index', compact('leavekData'));
    }

    // Approve telework HT
    public function approveLeaveHt(Request $request, $id)
    {
        $loggedInUser = auth()->user();
        $statusCommit = StatusCommit::find($id);

        if (!$statusCommit) {
            return back()->with('error', 'StatusCommit not found.');
        }

        // Ubah nilai status menjadi "preliminary"
        $statusCommit->update([
            'approver_id' => $loggedInUser->id,
            'status' => 'preliminary',
        ]);

        $message = $request->message;

        return redirect()->route('approveht.leaveHt')->with(['success' => "$message approved successfully"]);
    }

    // Reject telework HT
    public function rejectLeaveHt(Request $request, $id)
    {
        $loggedInUser = auth()->user();
        $statusCommit = StatusCommit::find($id);

        if (!$statusCommit) {
            return back()->with('error', 'StatusCommit not found.');
        }

        // Ubah nilai status menjadi "preliminary"
        $statusCommit->update([
            'approver_id' => $loggedInUser->id,
            'status' => 'rejected',
            'description' => $request->description,
        ]);

        $message = $request->message;

        return redirect()->route('approveht.leaveHt')->with(['success' => "$message rejected successfully"]);
    }
    // Leave Reject Ht



    // ------------------------------------------------------- Human Resource --------------------------------------------------------------- \\

    public function workTripHumanRes()
    {
        $loggedInUser = auth()->user();

        $kepaladivisi = null;
        if ($loggedInUser->employee) {
            $kepaladivisi = $loggedInUser->employee->division_id;
        }


        // Only fetch WorkTrip data
        $workTripDataQuery = Presence::with('worktrip.statusCommit')
            ->where('category', 'work_trip');

        // Condition to check if the user doesn't have the 'super-admin' role
        if (!$loggedInUser->hasRole('super-admin') && $kepaladivisi) {
            $workTripDataQuery->whereHas('user.employee', function ($employeeQuery) use ($kepaladivisi) {
                $employeeQuery->where('division_id', $kepaladivisi);
            });
        }

        $workTripData = $workTripDataQuery->whereHas('worktrip', function ($worktripQuery) {
            $today = Carbon::today('Asia/Jakarta');
            $worktripQuery->whereHas('statusCommit', function ($statusCommitQuery) {
                $statusCommitQuery->where('status', 'preliminary');
            })
                ->whereDate('start_date', '<=', $today) // Add condition for start_date
                ->whereDate('end_date', '>=', $today);
        })
            ->orderBy('entry_time', 'asc')
            ->paginate(10);


        return view('approve.humanResource.worktrip.index', compact('workTripData'));
    }


    // approve Hr
    public function approveWkHumanRes(Request $request, $id)
    {
        $loggedInUser = auth()->user();
        $statusCommit = StatusCommit::find($id);

        if (!$statusCommit) {
            return back()->with('error', 'StatusCommit not found.');
        }

        // Ubah nilai status menjadi "preliminary"
        $statusCommit->update([
            'approver_id' => $loggedInUser->id,
            // 'approver_id' => $request->approver_id,
            'status' => 'allowed',
            'description' => $request->description,
        ]);

        $message = $request->message;

        $statusCommit2 = StatusCommit::with('statusable')->findOrFail($id);
        $statusable = $statusCommit2->statusable;



        if ($statusable->presence) {
            // $statusable->update([
            //     'entry_time' => '08:30:00',
            // ]);

            if ($statusable->presence->category == 'work_trip' && $statusCommit2->status === 'allowed') {

                $startDate = Carbon::parse($statusable->start_date);
                $endDate = Carbon::parse($statusable->end_date);
                $submissionDate = Carbon::parse($statusable->presence->date);

                if (!$startDate->equalTo($submissionDate)) {
                    $statusable->presence->delete();
                }

                $currentDate = clone $startDate;
                while ($currentDate->lte($endDate)) {

                    $presenceForCurrentDate = Presence::firstOrNew([
                        'user_id' => $statusable->user_id,
                        'date' => $currentDate->toDateString()
                    ]);

                    // Check if $currentDate is equal to $submissionDate and set entry_time accordingly
                    if ($currentDate->equalTo($submissionDate)) {
                        $presenceForCurrentDate->entry_time = '08:30:00';
                    } else {
                        $presenceForCurrentDate->entry_time = '00:00:00';
                    }            
                    
                    $presenceForCurrentDate->exit_time = '17:30:00';
                    $presenceForCurrentDate->category = 'work_trip';
                    $presenceForCurrentDate->save();

                    if ($currentDate->equalTo($startDate)) {
                        $statusable->presence_id = $presenceForCurrentDate->id;
                        $statusable->save();
                    }

                    $currentDate->addDay();
                }
            }
        }

        // return response()->json(['message' => 'Success', 'data' => $presenceForCurrentDate], 200);
        return redirect()->route('approvehr.worktripHr')->with(['success' => "$message approved successfully"]);
    }

    // Reject Hr
    public function rejectWkHumanRes(Request $request, $id)
    {
        $loggedInUser = auth()->user();
        $statusCommit = StatusCommit::find($id);

        if (!$statusCommit) {
            return back()->with('error', 'StatusCommit not found.');
        }

        // Ubah nilai status menjadi "preliminary"
        $statusCommit->update([
            'approver_id' => $loggedInUser->id,
            'status' => 'rejected',
            'description' => $request->description,
        ]);

        $message = $request->message;

        return redirect()->route('approvehr.worktripHr')->with(['success' => "$message rejected successfully"]);
    }
    // end worktrip human resource


    public function teleworkHumanRes()
    {
        $loggedInUser = auth()->user();

        $kepaladivisi = null;
        if ($loggedInUser->employee) {
            $kepaladivisi = $loggedInUser->employee->division_id;
        }


        // Only fetch WorkTrip data
        $teleworkDataQuery = Presence::with('telework.statusCommit')
            ->where('category', 'telework');

        // Condition to check if the user doesn't have the 'super-admin' role
        if (!$loggedInUser->hasRole('super-admin') && $kepaladivisi) {
            $teleworkDataQuery->whereHas('user.employee', function ($employeeQuery) use ($kepaladivisi) {
                $employeeQuery->where('division_id', $kepaladivisi);
            });
        }

        $teleworkData = $teleworkDataQuery->whereHas('telework', function ($teleworkQuery) {
            $teleworkQuery->whereHas('statusCommit', function ($statusCommitQuery) {
                $statusCommitQuery->where('status', 'preliminary');
            });
        })
            ->orderBy('entry_time', 'asc')
            ->paginate(10);


        return view('approve.humanResource.telework.index', compact('teleworkData'));
    }


    public function approveTeleHumanRes(Request $request, $id)
    {
        $loggedInUser = auth()->user();
        $statusCommit = StatusCommit::find($id);

        if (!$statusCommit) {
            return back()->with('error', 'StatusCommit not found.');
        }

        // Ubah nilai status menjadi "preliminary"
        $statusCommit->update([
            'approver_id' => $loggedInUser->id,
            'status' => 'allowed',
            'description' => $request->description,
        ]);

        $message = $request->message;

        return redirect()->route('approvehr.teleworkHr')->with(['success' => "$message approved successfully"]);
    }


    public function rejectTeleHumanRes(Request $request, $id)
    {
        $loggedInUser = auth()->user();
        $statusCommit = StatusCommit::find($id);

        if (!$statusCommit) {
            return back()->with('error', 'StatusCommit not found.');
        }

        // Ubah nilai status menjadi "preliminary"
        $statusCommit->update([
            'approver_id' => $loggedInUser->id,
            'status' => 'rejected',
            'description' => $request->description,
        ]);

        $message = $request->message;

        return redirect()->route('approvehr.teleworkHr')->with(['success' => "$message rejected successfully"]);
    }


    public function leaveHumanRes()
    {
        $loggedInUser = auth()->user();

        $kepaladivisi = null;
        if ($loggedInUser->employee) {
            $kepaladivisi = $loggedInUser->employee->division_id;
        }


        // Only fetch WorkTrip data
        $leaveDataQuery = Presence::with('leave.statusCommit')
            ->where('category', 'leave');

        // Condition to check if the user doesn't have the 'super-admin' role
        if (!$loggedInUser->hasRole('super-admin') && $kepaladivisi) {
            $leaveDataQuery->whereHas('user.employee', function ($employeeQuery) use ($kepaladivisi) {
                $employeeQuery->where('division_id', $kepaladivisi);
            });
        }

        $leavekData = $leaveDataQuery->whereHas('leave', function ($leaveQuery) {
            $leaveQuery->whereHas('statusCommit', function ($statusCommitQuery) {
                $statusCommitQuery->where('status', 'preliminary');
            });
        })
            ->orderBy('entry_time', 'asc')
            ->paginate(10);

        return view('approve.humanResource.leave.index', compact('leavekData'));
    }

    public function approveLeaveHumanRes(Request $request, $id)
    {
        $loggedInUser = auth()->user();
        $statusCommit = StatusCommit::find($id);

        if (!$statusCommit) {
            return back()->with('error', 'StatusCommit not found.');
        }

        // Ubah nilai status menjadi "preliminary"
        $statusCommit->update([
            'approver_id' => $loggedInUser->id,
            'status' => 'allowed',
            'description' => $request->description,
        ]);

        $message = $request->message;

        $statusCommit2 = StatusCommit::with('statusable')->findOrFail($id);
        $statusable = $statusCommit2->statusable;

        if ($statusable->presence->category == 'leave' && $statusCommit2->status === 'allowed') {
            $startDate = Carbon::parse($statusable->start_date);
            $endDate = Carbon::parse($statusable->end_date);
            $currentDate = Carbon::today();
    
            if ($startDate->isToday()) {
                // If leave starts today, update today's presence
                $statusable->presence->update([
                    'entry_time' => '08:30:00',
                    'exit_time' => '17:30:00',
                    'category' => 'leave'
                ]);
            } else if ($startDate->greaterThan($currentDate)) {
                // If leave starts in future, delete today's presence (if exists)
                $statusable->presence->delete();
            }
    
            // Create or update presence records for the entire leave duration
            $currentDate = clone $startDate;
            while ($currentDate->lte($endDate)) {
                Presence::updateOrCreate([
                    'user_id' => $statusable->user_id,
                    'date' => $currentDate->toDateString(),
                    'category' => 'leave'
                ], [
                    'entry_time' => '08:30:00',
                    'exit_time' => '17:30:00'
                ]);
                $currentDate->addDay();
            }
        }
        // return response()->json(['message' => 'Success', 'data' => $presenceForCurrentDate], 200);
        return redirect()->route('approvehr.leaveHr')->with(['success' => "$message approved successfully"]);

    }

    public function rejectLeaveHumanRes(Request $request, $id)
    {
        $loggedInUser = auth()->user();
        $statusCommit = StatusCommit::find($id);

        if (!$statusCommit) {
            return back()->with('error', 'StatusCommit not found.');
        }

        // Ubah nilai status menjadi "preliminary"
        $statusCommit->update([
            'approver_id' => $loggedInUser->id,
            'status' => 'rejected',
            'description' => $request->description,
        ]);

        $message = $request->message;

        return redirect()->route('approvehr.leaveHr')->with(['success' => "$message rejected successfully"]);
    }
}
