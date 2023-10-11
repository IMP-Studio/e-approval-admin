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
            ->whereDate('date', '<=', $today);
        })
            ->orderBy('entry_time', 'asc')
            ->paginate(10);
            // ->map(function ($item) {
            // $item->category = 'work_trip';
            // return $item;
            // })

        if ($request->ajax()) {
            $query = $request->input('query');

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

            $workTripDataQuery->whereHas('user', function ($userQuery) use ($query) {
                $userQuery->where('name', 'LIKE', '%' . $query . '%');
            });
    
            $workTripData = $workTripDataQuery->whereHas('worktrip', function ($worktripQuery) {
                $today = Carbon::today('Asia/Jakarta');
                $worktripQuery->whereHas('statusCommit', function ($statusCommitQuery) {
                    $statusCommitQuery->where('status', 'pending');
                })
                ->whereDate('date', '<=', $today);
            })
                ->orderBy('entry_time', 'asc')
                ->paginate(10);;

                $output = '';
                $iteration = 0;

                foreach ($workTripData as $item) {
                    $iteration++;
                    $output = '<tr class="intro-x h-16">
                    <td class="w-4 text-center">' . $iteration . '.</td>
                    <td class="w-50 text-center capitalize">' . $item->user->name . '</td>
                    <td class="w-50 text-center capitalize">' . $item->user->employee->division->name . '</td>
                    <td class="w-50 text-center capitalize">' . ($item->category === 'work_trip' ? 'Work Trip' : $item->category) . '</td>
                    <td class="w-50 text-center capitalize">' . $item->worktrip->statusCommit->first()->status . '</td>
                    <td class="table-report__action w-56">
                        <div class="flex justify-center items-center">
                            <a data-wkHtid="' . $item->worktrip->statusCommit->first()->id . '" data-messageWK="' . $item->user->name . ' ' . $item->category . '" class="flex items-center text-success mr-3 approve_wk_Ht"
                                data-Positionid="" href="javascript:;" data-tw-toggle="modal"
                                data-tw-target="#modal-apprv-wt-search">
                                <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Approve
                            </a>
                            <a class="flex items-center text-warning delete-button mr-3 show-attendance-modal-search-worktrip"
                                data-avatar="' . $item->user->employee->avatar . '"
                                data-gender="' . $item->user->employee->gender . '"
                                data-firstname="' . $item->user->employee->first_name . '"
                                data-LastName="' . $item->user->employee->last_name . '"
                                data-stafId="' . $item->user->employee->id_number . '"
                                data-Category="' . ($item->category === 'work_trip' ? 'Work Trip' : $item->category) . '"
                                data-Position="' . $item->user->employee->position->name . '"
                                data-startDate="' . $item->worktrip->start_date . '"
                                data-endDate="' . $item->worktrip->end_date . '"
                                data-enrtyDate="' . $item->worktrip->entry_date . '"
                                data-file="' . $item->worktrip->file . '"
                                href="javascript:;" data-tw-toggle="modal" data-tw-target="#show-modal-approve-worktrip">
                                <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail
                            </a>';
                            
                    if (auth()->check() && auth()->user()->can('reject_presence')) {
                        $output .= '<a data-rejectwkHtid="' . $item->worktrip->statusCommit->first()->id . '" data-rejectmessageWK="' . $item->user->name . ' ' . $item->category . '" class="flex items-center text-danger reject_wk_Ht" data-id=""
                            data-name="" href="javascript:;" data-tw-toggle="modal"
                            data-tw-target="#reject-confirmation-modal">
                            <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Reject
                        </a>';
                    }

                    $output .= '</div>
                        </td>
                    </tr>';
                }

                return response($output);
        }

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
        // if (!$loggedInUser->hasRole('super-admin') && $kepaladivisi) {
        //     $workTripDataQuery->whereHas('user.employee', function ($employeeQuery) use ($kepaladivisi) {
        //         $employeeQuery->where('division_id', $kepaladivisi);
        //     });
        // }

        $workTripData = $workTripDataQuery->whereHas('worktrip', function ($worktripQuery) {
            $today = Carbon::today('Asia/Jakarta');
            $worktripQuery->whereHas('statusCommit', function ($statusCommitQuery) {
                $statusCommitQuery->where('status', 'preliminary');
            })
            ->whereDate('date', '<=', $today);
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


// telework
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
        // if (!$loggedInUser->hasRole('super-admin') && $kepaladivisi) {
        //     $teleworkDataQuery->whereHas('user.employee', function ($employeeQuery) use ($kepaladivisi) {
        //         $employeeQuery->where('division_id', $kepaladivisi);
        //     });
        // }

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
        // if (!$loggedInUser->hasRole('super-admin') && $kepaladivisi) {
        //     $leaveDataQuery->whereHas('user.employee', function ($employeeQuery) use ($kepaladivisi) {
        //         $employeeQuery->where('division_id', $kepaladivisi);
        //     });
        // }

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
