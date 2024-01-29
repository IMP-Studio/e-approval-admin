<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Leave;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Presence;
use App\Models\Telework;
use App\Models\WorkTrip;
use App\Models\StatusCommit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Mail\RequestLeaveEmail;
use App\Mail\RequestPresenceEmail;
use App\Mail\ResultSubmissionEmail;
use App\Jobs\SendResultSubmissionEmailJob;

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
            ->paginate()->all();


        return view('approve.headTired.worktrip.index', compact('workTripData'));
    }

    // approve Ht
    public function approveWkHt(Request $request, $id)
    {
        try {
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
            
            $user = User::with(['employee'])->where('id', $statusCommit->statusable->user_id)->first(); 

            $workTrip = WorkTrip::with('presence', 'statusCommit')
                ->whereHas('statusCommit', function ($query) use ($statusCommit) {
                    $query->where('statusable_type', 'App\Models\WorkTrip')
                        ->where('statusable_id', $statusCommit->statusable_id);
                })
                ->first();

            $presence = Presence::with('worktrip')->where('id', $workTrip->presence_id)->first();

            $message = $request->message;

            // KIRIM EMAIL KE HR

            $requiredPermissions = [
                'approve_allowed',
                'view_request_preliminary',
            ];
            $approvers = User::with(['employee', 'permissions'])->where(function ($query) use ($requiredPermissions) {
                foreach ($requiredPermissions as $permission) {
                    $query->orWhereHas('permissions', function ($query) use ($permission) {
                        $query->where('name', $permission);
                    });
                }
            })
                ->get();
            
            foreach ($approvers as $approver) {
                \Mail::to($approver->email)->send(new RequestPresenceEmail($presence, $user, $approver, $workTrip, null));
            }

            return redirect()->route('approveht.worktripHt')->with(['success' => "$message approved successfully"]);
        } catch (\Exception $e) {
            \Log::error('An error occurred: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile());
            \Log::error('Line: ' . $e->getLine());
        
            return back()->with('error', 'An error occurred while processing the request.');
        }
    }

    // Reject Ht
    public function rejectWkHt(Request $request, $id)
    {
        try {
            $loggedInUser = auth()->user();
            $statusCommit = StatusCommit::find($id);

            if (!$statusCommit) {
                return back()->with('error', 'StatusCommit not found.');
            }

            // Ubah nilai status menjadi "rejected"
            $statusCommit->update([
                'approver_id' => $loggedInUser->id,
                'status' => 'rejected',
                'description' => $request->description,
            ]);

            $user = User::with(['employee'])->where('id', $statusCommit->statusable->user_id)->first();

            $workTrip = WorkTrip::with('presence', 'statusCommit')
                ->whereHas('statusCommit', function ($query) use ($statusCommit) {
                    $query->where('statusable_type', 'App\Models\WorkTrip')
                        ->where('statusable_id', $statusCommit->statusable_id);
                })
                ->first();

            $presence = Presence::with('worktrip')->where('id', $workTrip->presence_id)->first();

            $message = $request->message;

            // KIRIM EMAIL KE HR

            $requiredPermissions = [
                'approve_allowed',
                'view_request_preliminary',
            ];
            $approvers = User::with(['employee', 'permissions'])->where(function ($query) use ($requiredPermissions) {
                foreach ($requiredPermissions as $permission) {
                    $query->orWhereHas('permissions', function ($query) use ($permission) {
                        $query->where('name', $permission);
                    });
                }
            })
                ->get();
            
            foreach ($approvers as $approver) {
                \Mail::to($approver->email)->send(new RequestPresenceEmail($presence, $user, $approver, $workTrip, null));
            }

            // Kirim mail ke user
            \Mail::to($user->email)->send(new ResultSubmissionEmail($presence, $user, $workTrip, null, null));

            return redirect()->route('approveht.worktripHt')->with(['success' => "$message approved successfully"]);

        } catch (\Exception $e) {
            \Log::error('An error occurred: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile());
            \Log::error('Line: ' . $e->getLine());
        
            return back()->with('error', 'An error occurred while processing the request.');
        }
        
    }
    // worktripHt end \\

    // telework Ht
    public function teleworkHt(Request $request)
    {
        $loggedInUser = auth()->user();

        $kepaladivisi = null;
        if ($loggedInUser->employee) {
            $kepaladivisi = $loggedInUser->employee->division_id;
        }


        // Only fetch Telework data
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
            ->paginate(5);

        return view('approve.headTired.telework.index', compact('teleworkData'));
    }

    // approve Telework
    public function approveTeleHt(Request $request, $id)
    {
        try {
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

            $user = User::with(['employee'])->where('id', $statusCommit->statusable->user_id)->first(); 

            $telework = Telework::with('presence', 'statusCommit')
                ->whereHas('statusCommit', function ($query) use ($statusCommit) {
                    $query->where('statusable_type', 'App\Models\Telework')
                        ->where('statusable_id', $statusCommit->statusable_id);
                })
                ->first();

            $presence = Presence::with('telework')->where('id', $telework->presence_id)->first();

            $message = $request->message;

            // KIRIM EMAIL KE HR

            $requiredPermissions = [
                'approve_allowed',
                'view_request_preliminary',
            ];
            $approvers = User::with(['employee', 'permissions'])->where(function ($query) use ($requiredPermissions) {
                foreach ($requiredPermissions as $permission) {
                    $query->orWhereHas('permissions', function ($query) use ($permission) {
                        $query->where('name', $permission);
                    });
                }
            })
                ->get();
            
            foreach ($approvers as $approver) {
                \Mail::to($approver->email)->send(new RequestPresenceEmail($presence, $user, $approver, null, $telework));
            }

            return redirect()->route('approveht.teleworkHt')->with(['success' => "$message rejected successfully"]);

        } catch (\Exception $e)  {
            \Log::error('An error occurred: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile());
            \Log::error('Line: ' . $e->getLine());
        
            return back()->with('error', 'An error occurred while processing the request.');
        }
    }

    // Reject Telework
    public function rejectTeleHt(Request $request, $id)
    {
        try {
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
    
            $user = User::with(['employee'])->where('id', $statusCommit->statusable->user_id)->first(); 

            $telework = Telework::with('presence', 'statusCommit')
                ->whereHas('statusCommit', function ($query) use ($statusCommit) {
                    $query->where('statusable_type', 'App\Models\Telework')
                        ->where('statusable_id', $statusCommit->statusable_id);
                })
                ->first();

            $presence = Presence::with('telework')->where('id', $telework->presence_id)->first();

            $message = $request->message;

            // KIRIM EMAIL KE HR

            $requiredPermissions = [
                'approve_allowed',
                'view_request_preliminary',
            ];
            $approvers = User::with(['employee', 'permissions'])->where(function ($query) use ($requiredPermissions) {
                foreach ($requiredPermissions as $permission) {
                    $query->orWhereHas('permissions', function ($query) use ($permission) {
                        $query->where('name', $permission);
                    });
                }
            })
                ->get();
            
            foreach ($approvers as $approver) {
                \Mail::to($approver->email)->send(new RequestPresenceEmail($presence, $user, $approver, null, $telework));
            }

            // Kirim mail ke user
            \Mail::to($user->email)->send(new ResultSubmissionEmail($presence, $user, null, $telework, null));

            return redirect()->route('approveht.teleworkHt')->with(['success' => "$message rejected successfully"]);

        } catch (\Exception $e) {
            \Log::error('An error occurred: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile());
            \Log::error('Line: ' . $e->getLine());
        
            return back()->with('error', 'An error occurred while processing the request.');
        }
    }
    // TeleWork Ht

    // Leave HT  
    public function leaveHt(Request $request)
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
            ->paginate()->all();
        
        return view('approve.headTired.leave.index', compact('leavekData'));
    }

    // Approve telework HT
    public function approveLeaveHt(Request $request, $id)
    {
        try {
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

            $user = User::with(['employee'])->where('id', $statusCommit->statusable->user_id)->first(); 

            $leave = Leave::with('presence', 'statusCommit')
                ->whereHas('statusCommit', function ($query) use ($statusCommit) {
                    $query->where('statusable_type', 'App\Models\Leave')
                        ->where('statusable_id', $statusCommit->statusable_id);
                })
                ->first();

            $presence = Presence::with('leave')->where('id', $leave->presence_id)->first();

            $message = $request->message;

            // KIRIM EMAIL KE HR

            $requiredPermissions = [
                'approve_allowed',
                'view_request_preliminary',
            ];
            $approvers = User::with(['employee', 'permissions'])->where(function ($query) use ($requiredPermissions) {
                foreach ($requiredPermissions as $permission) {
                    $query->orWhereHas('permissions', function ($query) use ($permission) {
                        $query->where('name', $permission);
                    });
                }
            })
                ->get();
            
            foreach ($approvers as $approver) {
                \Mail::to($approver->email)->send(new RequestLeaveEmail($presence, $user, $approver, $leave));
            }

            return redirect()->route('approveht.leaveHt')->with(['success' => "$message approved successfully"]);

        } catch (\Exception $e) {
            \Log::error('An error occurred: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile());
            \Log::error('Line: ' . $e->getLine());
        
            return back()->with('error', 'An error occurred while processing the request.');
        }
    }

    // Reject telework HT
    public function rejectLeaveHt(Request $request, $id)
    {
        try {
            $loggedInUser = auth()->user();
            $statusCommit = StatusCommit::find($id);

            if (!$statusCommit) {
                return back()->with('error', 'StatusCommit not found.');
            }

            // Ubah nilai status menjadi "rejected"
            $statusCommit->update([
                'approver_id' => $loggedInUser->id,
                'status' => 'rejected',
                'description' => $request->description,
            ]);
            
            $user = User::with(['employee'])->where('id', $statusCommit->statusable->user_id)->first();

            $leave = Leave::with('presence', 'statusCommit')
                ->whereHas('statusCommit', function ($query) use ($statusCommit) {
                    $query->where('statusable_type', 'App\Models\Leave')
                        ->where('statusable_id', $statusCommit->statusable_id);
                })
                ->first();

            $presence = Presence::with('leave')->where('id', $leave->presence_id)->first();

            $message = $request->message;

            // KIRIM EMAIL KE HR

            $requiredPermissions = [
                'approve_allowed',
                'view_request_preliminary',
            ];
            $approvers = User::with(['employee', 'permissions'])->where(function ($query) use ($requiredPermissions) {
                foreach ($requiredPermissions as $permission) {
                    $query->orWhereHas('permissions', function ($query) use ($permission) {
                        $query->where('name', $permission);
                    });
                }
            })
                ->get();
            
            foreach ($approvers as $approver) {
                \Mail::to($approver->email)->send(new RequestLeaveEmail($presence, $user, $approver, $leave));
            }

            // Kirim mail ke user
            \Mail::to($user->email)->send(new ResultSubmissionEmail($presence, $user, null, null, $leave));

            return redirect()->route('approveht.leaveHt')->with(['success' => "$message approved successfully"]);

        } catch (\Exception $e) {
            \Log::error('An error occurred: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile());
            \Log::error('Line: ' . $e->getLine());
        
            return back()->with('error', 'An error occurred while processing the request.');
        }
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

   
        $workTripData = $workTripDataQuery->whereHas('worktrip', function ($worktripQuery) {
            $today = Carbon::today('Asia/Jakarta');
            $worktripQuery->whereHas('statusCommit', function ($statusCommitQuery) {
                $statusCommitQuery->where('status', 'preliminary');
            })
            ->whereDate('date', '<=', $today);
        })
        ->get();


        return view('approve.humanResource.worktrip.index', compact('workTripData'));
    }


    // approve Hr
    public function approveWkHumanRes(Request $request, $id = null)
    {
        try {
            $loggedInUser = auth()->user();
            $all_ids = $request->ids;

            if ($all_ids) {
                foreach ($all_ids as $id) {
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
        
                    $statusCommit2 = StatusCommit::with('statusable')->findOrFail($id);
                    $statusable = $statusCommit2->statusable;
        
                    if ($statusable->presence) {
                        if ($statusable->presence->category == 'work_trip' && $statusCommit2->status === 'allowed') {
                            $submissionDate = Carbon::parse($statusable->presence->date);
        
                            $presenceForCurrentDate = Presence::firstOrNew([
                                'user_id' => $statusable->user_id,
                                'date' => $submissionDate->toDateString()
                            ]);
        
                            $presenceForCurrentDate->entry_time = '08:30:00';
                            $presenceForCurrentDate->exit_time = '17:30:00';
                            $presenceForCurrentDate->category = 'work_trip';
                            $presenceForCurrentDate->save();
        
                            $statusable->presence_id = $presenceForCurrentDate->id;
                            $statusable->save();
                        }
                    }
        
                    $user = User::with(['employee'])->where('id', $statusCommit->statusable->user_id)->first();
        
                    $workTrip = WorkTrip::with('presence', 'statusCommit')
                        ->whereHas('statusCommit', function ($query) use ($statusCommit) {
                            $query->where('statusable_type', 'App\Models\WorkTrip')
                                ->where('statusable_id', $statusCommit->statusable_id);
                        })
                        ->first();
        
                    $presence = Presence::with('worktrip')->where('id', $workTrip->presence_id)->first();
        
                    $message = $request->message;
                    
                    // Kirim mail ke user
                    dispatch(new SendResultSubmissionEmailJob($presence, $user,$workTrip,null, null));
                }
            }elseif($id !== null) {
                # singel data

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
    
                $statusCommit2 = StatusCommit::with('statusable')->findOrFail($id);
                $statusable = $statusCommit2->statusable;
    
                if ($statusable->presence) {
                    if ($statusable->presence->category == 'work_trip' && $statusCommit2->status === 'allowed') {
                        $submissionDate = Carbon::parse($statusable->presence->date);
    
                        $presenceForCurrentDate = Presence::firstOrNew([
                            'user_id' => $statusable->user_id,
                            'date' => $submissionDate->toDateString()
                        ]);
    
                        $presenceForCurrentDate->entry_time = '08:30:00';
                        $presenceForCurrentDate->exit_time = '17:30:00';
                        $presenceForCurrentDate->category = 'work_trip';
                        $presenceForCurrentDate->save();
    
                        $statusable->presence_id = $presenceForCurrentDate->id;
                        $statusable->save();
                    }
                }
    
                $user = User::with(['employee'])->where('id', $statusCommit->statusable->user_id)->first();
    
                $workTrip = WorkTrip::with('presence', 'statusCommit')
                    ->whereHas('statusCommit', function ($query) use ($statusCommit) {
                        $query->where('statusable_type', 'App\Models\WorkTrip')
                            ->where('statusable_id', $statusCommit->statusable_id);
                    })
                    ->first();
    
                $presence = Presence::with('worktrip')->where('id', $workTrip->presence_id)->first();
    
                $message = $request->message;
                
                // Kirim mail ke user
                dispatch(new SendResultSubmissionEmailJob($presence, $user,$workTrip,null, null));
            }else {
                return back()->with('error', 'Invalid request.');
            }

            return redirect()->route('approvehr.worktripHr')->with(['success' => "$message approved successfully"]);
        } catch (\Exception $e) {
            \Log::error('An error occurred: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile());
            \Log::error('Line: ' . $e->getLine());
        
            return back()->with('error', 'An error occurred while processing the request.');
        }
        
    }


    // Reject Hr
    public function rejectWkHumanRes(Request $request, $id = null)
    {
        try {
            $loggedInUser = auth()->user();
            $all_ids = $request->ids;

            if ($all_ids) {
                foreach ($all_ids as $id) {
                    # multiple data
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
        
                    $user = User::with(['employee'])->where('id', $statusCommit->statusable->user_id)->first();
        
                    $workTrip = WorkTrip::with('presence', 'statusCommit')
                        ->whereHas('statusCommit', function ($query) use ($statusCommit) {
                            $query->where('statusable_type', 'App\Models\WorkTrip')
                                ->where('statusable_id', $statusCommit->statusable_id);
                        })
                        ->first();
        
                    $presence = Presence::with('worktrip')->where('id', $workTrip->presence_id)->first();
        
                    $message = $request->message;
                    
                    // Kirim mail ke user
                    dispatch(new SendResultSubmissionEmailJob($presence, $user,$workTrip,null, null));
                }
            }elseif ($id !== null) {
                # single data
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
    
                $user = User::with(['employee'])->where('id', $statusCommit->statusable->user_id)->first();
    
                $workTrip = WorkTrip::with('presence', 'statusCommit')
                    ->whereHas('statusCommit', function ($query) use ($statusCommit) {
                        $query->where('statusable_type', 'App\Models\WorkTrip')
                            ->where('statusable_id', $statusCommit->statusable_id);
                    })
                    ->first();
    
                $presence = Presence::with('worktrip')->where('id', $workTrip->presence_id)->first();
    
                $message = $request->message;
                
                // Kirim mail ke user
                dispatch(new SendResultSubmissionEmailJob($presence, $user,$workTrip,null, null));
            }else {
                return back()->with('error', 'Invalid request.');
            }

            return redirect()->route('approvehr.worktripHr')->with(['success' => "$message rejected successfully"]);
        } catch (\Exception $e) {
            \Log::error('An error occurred: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile());
            \Log::error('Line: ' . $e->getLine());
        
            return back()->with('error', 'An error occurred while processing the request.');
        }
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

        $teleworkData = $teleworkDataQuery->whereHas('telework', function ($teleworkQuery) {
            $teleworkQuery->whereHas('statusCommit', function ($statusCommitQuery) {
                $statusCommitQuery->where('status', 'preliminary');
            });
        })
        ->get();


        return view('approve.humanResource.telework.index', compact('teleworkData'));
    }


    public function approveTeleHumanRes(Request $request, $id = null)
    {
        try {
            $loggedInUser = auth()->user();
            $ids = $request->ids;

            if ($ids) {
                 # multiple data
                foreach ($ids as $id) {
                    $statusCommit = StatusCommit::with('statusable')->find($id);
            
                    if (!$statusCommit) {
                        return back()->with('error', 'StatusCommit not found.');
                    }
            
                    $statusCommit->update([
                        'approver_id' => $loggedInUser->id,
                        'status' => 'allowed',
                        'description' => $request->description,
                    ]);
                    
                    $statusCommit->statusable->presence->update([
                        'entry_time' => '08:30'
                    ]);
            
                    $message = $request->message;
        
                    $user = User::with(['employee'])->where('id', $statusCommit->statusable->user_id)->first(); 
        
                    $telework = Telework::with('presence', 'statusCommit')
                        ->whereHas('statusCommit', function ($query) use ($statusCommit) {
                            $query->where('statusable_type', 'App\Models\Telework')
                                ->where('statusable_id', $statusCommit->statusable_id);
                        })
                        ->first();
        
                    $presence = Presence::with('telework')->where('id', $telework->presence_id)->first();
        
                    // Kirim mail ke user
                    dispatch(new SendResultSubmissionEmailJob($presence, $user,null ,$telework, null));
                }
            } elseif ($id !== null) {
                $statusCommit = StatusCommit::with('statusable')->find($id);
    
                if (!$statusCommit) {
                    return back()->with('error', 'StatusCommit not found.');
                }
        
                $statusCommit->update([
                    'approver_id' => $loggedInUser->id,
                    'status' => 'allowed',
                    'description' => $request->description,
                ]);
                
                $statusCommit->statusable->presence->update([
                    'entry_time' => '08:30'
                ]);
        
                $message = $request->message;
    
                $user = User::with(['employee'])->where('id', $statusCommit->statusable->user_id)->first(); 
    
                $telework = Telework::with('presence', 'statusCommit')
                    ->whereHas('statusCommit', function ($query) use ($statusCommit) {
                        $query->where('statusable_type', 'App\Models\Telework')
                            ->where('statusable_id', $statusCommit->statusable_id);
                    })
                    ->first();
    
                $presence = Presence::with('telework')->where('id', $telework->presence_id)->first();
    
                // Kirim mail ke user
                dispatch(new SendResultSubmissionEmailJob($presence, $user,null ,$telework, null));
            }else {
                return back()->with('error', 'Invalid request.');
            }
    
            return redirect()->route('approvehr.teleworkHr')->with(['success' => "$message approved successfully"]);
        } catch (\Exception $e) {
            \Log::error('An error occurred: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile());
            \Log::error('Line: ' . $e->getLine());
        
            return back()->with('error', 'An error occurred while processing the request.');
        }
       
    }


    public function rejectTeleHumanRes(Request $request, $id = null)
    {
        try {
            $loggedInUser = auth()->user();
            $ids = $request->ids;

            if ($ids) {
                # multiple data
                foreach ($ids as $id) {
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

                    $user = User::with(['employee'])->where('id', $statusCommit->statusable->user_id)->first(); 

                    $telework = Telework::with('presence', 'statusCommit')
                        ->whereHas('statusCommit', function ($query) use ($statusCommit) {
                            $query->where('statusable_type', 'App\Models\Telework')
                                ->where('statusable_id', $statusCommit->statusable_id);
                        })
                        ->first();

                    $presence = Presence::with('telework')->where('id', $telework->presence_id)->first();

                    // Kirim mail ke user
                    dispatch(new SendResultSubmissionEmailJob($presence, $user,null ,$telework, null));
                }
            } elseif ($id !== null) {
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
    
                $user = User::with(['employee'])->where('id', $statusCommit->statusable->user_id)->first(); 
    
                $telework = Telework::with('presence', 'statusCommit')
                    ->whereHas('statusCommit', function ($query) use ($statusCommit) {
                        $query->where('statusable_type', 'App\Models\Telework')
                            ->where('statusable_id', $statusCommit->statusable_id);
                    })
                    ->first();
    
                $presence = Presence::with('telework')->where('id', $telework->presence_id)->first();
    
                // Kirim mail ke user
                dispatch(new SendResultSubmissionEmailJob($presence, $user,null ,$telework, null));
            } else {
                return back()->with('error', 'Invalid request.');
            }

            return redirect()->route('approvehr.teleworkHr')->with(['success' => "$message rejected successfully"]);
        } catch (\Exception $e) {
            \Log::error('An error occurred: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile());
            \Log::error('Line: ' . $e->getLine());
        
            return back()->with('error', 'An error occurred while processing the request.');
        }
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

        $leavekData = $leaveDataQuery->whereHas('leave', function ($leaveQuery) {
            $leaveQuery->whereHas('statusCommit', function ($statusCommitQuery) {
                $statusCommitQuery->where('status', 'preliminary');
            });
        })
            ->orderBy('entry_time', 'asc')
            ->paginate(10);

        return view('approve.humanResource.leave.index', compact('leavekData'));
    }

    private function getHoliday($startYear, $endYear) {
        $holidays = [];
        for ($year = $startYear; $year <= $endYear; $year++) {
            $apiUrl = "https://api-harilibur.vercel.app/api?year={$year}";
            $response = file_get_contents($apiUrl);
            if ($response) {
                $holidayData = json_decode($response, true);
                if ($holidayData) {
                    $holidays = array_merge(
                        $holidays,
                        array_filter($holidayData, function ($holiday) {
                            return isset($holiday['is_national_holiday']) ? $holiday['is_national_holiday'] === true : true;
                        })
                    );
                } else {
                    throw new \Exception('Failed to parse JSON response for national holidays.');
                }
            } else {
                throw new \Exception('Failed to fetch data from the API for national holidays.');
            }
        }

        return $holidays;
    }
    
    private function isNationalHoliday($date, $nationalHolidays) {
        $formattedDate = $date->toDateString();
        $nationalHolidayDates = array_map(function ($holiday) {
            return Carbon::parse($holiday['holiday_date'])->toDateString();
        }, $nationalHolidays);
    
        return in_array($formattedDate, $nationalHolidayDates);
    }

    public function approveLeaveHumanRes(Request $request, $id)
    {
        try {
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
                $currentDate = clone $startDate;
                $nationalHolidays = $this->getHoliday($currentDate->year, $endDate->year);
            
                while ($currentDate->lte($endDate)) {
                    if ($currentDate->isWeekend()) {
                        $currentDate->addDay();
                        continue;
                    }
            
                    if ($this->isNationalHoliday($currentDate, $nationalHolidays)) {
                        $currentDate->addDay();
                        continue;
                    }
            
                    Presence::updateOrCreate(
                        [
                            'user_id' => $statusable->user_id,
                            'date' => $currentDate->toDateString(),
                            'category' => 'leave'
                        ],
                        [
                            'entry_time' => '08:30:00',
                            'exit_time' => '17:30:00'
                        ]
                    );
            
                    $currentDate->addDay();
                }
            }

            $user = User::with(['employee'])->where('id', $statusCommit->statusable->user_id)->first();

            $leave = Leave::with('presence', 'statusCommit')
                ->whereHas('statusCommit', function ($query) use ($statusCommit) {
                    $query->where('statusable_type', 'App\Models\Leave')
                        ->where('statusable_id', $statusCommit->statusable_id);
                })
                ->first();

            $presence = Presence::with('leave')->where('id', $leave->presence_id)->first();

            $message = $request->message;

            // Kirim mail ke user
            dispatch(new SendResultSubmissionEmailJob($presence, $user,null,null, $leave));
            
            return redirect()->route('approvehr.leaveHr')->with(['success' => "$message approved successfully"]);
        } catch (\Exception $e) {
            \Log::error('An error occurred: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile());
            \Log::error('Line: ' . $e->getLine());
        
            return back()->with('error', 'An error occurred while processing the request.');
        }
    }

    public function rejectLeaveHumanRes(Request $request, $id)
    {
        try {
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
    
            $user = User::with(['employee'])->where('id', $statusCommit->statusable->user_id)->first();

            $leave = Leave::with('presence', 'statusCommit')
                ->whereHas('statusCommit', function ($query) use ($statusCommit) {
                    $query->where('statusable_type', 'App\Models\Leave')
                        ->where('statusable_id', $statusCommit->statusable_id);
                })
                ->first();

            $presence = Presence::with('leave')->where('id', $leave->presence_id)->first();

            $message = $request->message;

            // Kirim mail ke user
            dispatch(new SendResultSubmissionEmailJob($presence, $user,null,null, $leave));
           
            return redirect()->route('approvehr.leaveHr')->with(['success' => "$message rejected successfully"]);
            
        } catch (\Exception $e) {
            \Log::error('An error occurred: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile());
            \Log::error('Line: ' . $e->getLine());
        
            return back()->with('error', 'An error occurred while processing the request.');
        }
    }
}