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
                $userQuery->where('name','LIKE', '%' . $query . '%');
            });
    
            $workTripData = $workTripDataQuery->whereHas('worktrip', function ($worktripQuery) {
                $today = Carbon::today('Asia/Jakarta');
                $worktripQuery->whereHas('statusCommit', function ($statusCommitQuery) {
                    $statusCommitQuery->where('status', 'pending');
                })
                ->whereDate('date', '<=', $today);
            })
                ->orderBy('entry_time', 'asc')
                ->paginate(10);

                $output = '';
                $iteration = 0;

                foreach ($workTripData as $item) {
                    $iteration++;
                    $output .= '<tr class="intro-x h-16">
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
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="check-square" data-lucide="check-square" class="lucide lucide-check-square w-4 h-4 mr-1"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg> Approve
                            </a>
                            <a class="flex items-center text-warning delete-button mr-3 show-attendance-modal-search-worktrip"
                                data-avatar="' . $item->user->employee->avatar . '"
                                data-gender="' . $item->user->employee->gender . '"
                                data-firstname="' . $item->user->employee->first_name . '"
                                data-LastName="' . $item->user->employee->last_name . '"
                                data-stafId="' . $item->user->employee->id_number . '"
                                data-Category="' . ($item->category === 'work_trip' ? 'Work Trip' : $item->category) . '"
                                data-Position="' . $item->user->employee->position->name . '"
                                data-file="' . $item->worktrip->file . '"
                                href="javascript:;" data-tw-toggle="modal" data-tw-target="#show-modal-approve-worktrip">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="check-square" data-lucide="check-square" class="lucide lucide-check-square w-4 h-4 mr-1"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg> Detail
                            </a>';
                            
                    if (auth()->check() && auth()->user()->can('reject_presence')) {
                        $output .= '<a data-rejectwkHtid="' . $item->worktrip->statusCommit->first()->id . '" data-rejectmessageWK="' . $item->user->name . ' ' . $item->category . '" class="flex items-center text-danger reject_wk_Ht" data-id=""
                            data-name="" href="javascript:;" data-tw-toggle="modal"
                            data-tw-target="#reject-confirmation-modal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="check-square" data-lucide="check-square" class="lucide lucide-check-square w-4 h-4 mr-1"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg> Reject
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
            ->paginate(10);

        if ($request->ajax()) {
            $query = $request->input('query');

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

            $teleworkDataQuery->whereHas('user', function ($userQuery) use ($query) {
                $userQuery->where('name','LIKE', '%' . $query . '%');
            });

            $teleworkData = $teleworkDataQuery->whereHas('telework', function ($teleworkQuery) {
                $teleworkQuery->whereHas('statusCommit', function ($statusCommitQuery) {
                    $statusCommitQuery->where('status', 'pending');
                });
            })
            ->orderBy('entry_time', 'asc')
            ->paginate(10);

            $output = '';
            $iteration = 0;
            
            foreach ($teleworkData as $item) {
                $iteration++;
                $output .= '<tr class="intro-x h-16">
                <td class="w-4 text-center">' . $iteration . '.</td>
                <td class="w-50 text-center capitalize">' . $item->user->name . '</td>
                <td class="w-50 text-center capitalize">' . $item->user->employee->division->name . '</td>
                <td class="w-50 text-center capitalize">' . $item->category . '</td>
                <td class="w-50 text-center capitalize">' . $item->telework->statusCommit->first()->status . '</td>
                <td class="table-report__action w-56">
                    <div class="flex justify-center items-center">
                        <a data-teleHtid="' . $item->telework->statusCommit->first()->id . '" data-messageTele="' . $item->user->name . ' ' . $item->category . '" class="flex items-center text-success mr-3 approve_tele_Ht"
                            data-Positionid="" href="javascript:;" data-tw-toggle="modal"
                            data-tw-target="#modal-apprv-teleHt-search">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="check-square" data-lucide="check-square" class="lucide lucide-check-square w-4 h-4 mr-1"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg> Approve
                        </a>
                        <a class="flex items-center text-warning delete-button mr-3 show-attendance-modal-search-telework"
                            data-avatar="' . $item->user->employee->avatar . '"
                            data-divisi="' . $item->user->employee->division->name . '"
                            data-gender="' . $item->user->employee->gender . '"
                            data-date=" ' . $item->telework->presence->date . '"
                            data-firstname="' . $item->user->employee->first_name . '"
                            data-LastName="' . $item->user->employee->last_name . '"
                            data-stafId="' . $item->user->employee->id_number . '"
                            data-Category="' . ($item->category === 'work_trip' ? 'Work Trip' : $item->category) . '"
                            data-Position="' . $item->user->employee->position->name . '"
                            data-teleCategory="' . $item->telework->telework_category . '"
                            data-tempoEntry="' . $item->temporary_entry_time . '"
                            data-catDesc="' . $item->telework->category_description . '"
                            href="javascript:;" data-tw-toggle="modal" data-tw-target="#show-modal-search-telework">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="eye" data-lucide="eye" class="lucide lucide-eye w-4 h-4 mr-1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> Detail
                        </a>';
            
                if (auth()->check() && auth()->user()->can('reject_presence')) {
                    $output .= '<a data-rejectTeleHtid="' . $item->telework->statusCommit->first()->id . '" data-rejectmessageTele="' . $item->user->name . ' ' . $item->category . '" class="flex items-center text-danger reject_tele_Ht" href="javascript:;" data-tw-toggle="modal"
                        data-tw-target="#reject-confirmation-teleHt-modal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="trash-2" data-lucide="trash-2" class="lucide lucide-trash-2 w-4 h-4 mr-1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> Reject
                    </a>';
                }
            
                $output .= '</div>
                    </td>
                </tr>';
            }
            
            return response($output);
        }            


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
            ->paginate(10);
        
        if ($request->ajax()) {
            $query = $request->input('query');

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

            $leaveDataQuery->whereHas('user', function ($userQuery) use ($query) {
                $userQuery->where('name','LIKE', '%' . $query . '%');
            });
    
            $leavekData = $leaveDataQuery->whereHas('leave', function ($leaveQuery) {
                $leaveQuery->whereHas('statusCommit', function ($statusCommitQuery) {
                    $statusCommitQuery->where('status', 'pending');
                });
            })
            ->orderBy('entry_time', 'asc')
            ->paginate(10);

            $output = '';
            $iteration = 0;

            foreach ($leavekData as $item) {
                $iteration++;
                $output .= '<tr class="intro-x h-16">
                    <td class="w-4 text-center">' . $iteration . '.</td>
                    <td class="w-50 text-center capitalize">' . $item->user->name . '</td>
                    <td class="w-50 text-center capitalize">' . $item->user->employee->division->name . '</td>
                    <td class="w-50 text-center capitalize">' . $item->category . '</td>
                    <td class="w-50 text-center capitalize">' . $item->leave->statusCommit->first()->status . '</td>
                    <td class="table-report__action w-56">
                        <div class="flex justify-center items-center">
                            <a data-leaveHtid="' . $item->leave->statusCommit->first()->id . '"
                                data-messageLeaveHt="' . $item->user->name . ' ' . $item->category . '"
                                class="flex items-center text-success mr-3 approve_leave_Ht" data-Positionid=""
                                href="javascript:;" data-tw-toggle="modal"
                                data-tw-target="#modal-apprv-leave-search">
                                <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Approve
                            </a>
                            <a class="flex items-center text-warning mr-3 show-modal-search-leave"
                                data-avatar="' . $item->user->employee->avatar . '"
                                data-gender="' . $item->user->employee->gender . '"
                                data-firstname="' . $item->user->employee->first_name . '"
                                data-LastName="' . $item->user->employee->last_name . '"
                                data-stafId="' . $item->user->employee->id_number . '"
                                data-Category="' . ($item->category === 'work_trip' ? 'Work Trip' : $item->category) . '"
                                data-Position="' . $item->user->employee->position->name . '"
                                data-startDate="' . $item->leave->start_date . '"
                                data-endDate="' . $item->leave->end_date . '"
                                data-entryDate="' . $item->leave->entry_date . '"
                                data-typeLeave="' . $item->leave->leavedetail->description_leave . '"
                                data-typeDesc="' . $item->leave->leavedetail->typeofleave->leave_name . '"
                                data-submisDate="' . $item->leave->submission_date . '"
                                data-totalDays="' . $item->leave->leavedetail->days . '" href="javascript:;"
                                data-tw-toggle="modal" data-tw-target="#show-modal-leaveht">
                                <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail
                            </a>';

                if (auth()->check() && auth()->user()->can('reject_presence')) {
                    $output .= '<a data-rejectLeaveHtid="' . $item->leave->statusCommit->first()->id . '"
                        data-rejectmessageLeaveHt="' . $item->user->name . ' ' . $item->category . '"
                        class="flex items-center text-danger reject_leave_Ht" data-id=""
                        data-name="" href="javascript:;" data-tw-toggle="modal"
                        data-tw-target="#reject-confirmation-leave-modal">
                        <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Reject
                    </a>';
                }

                $output .= '</div>
                    </td>
                </tr>';
            }

            return response($output);
        }

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
        'status' => 'allowed',
        'description' => $request->description,
    ]);

    $message = $request->message;

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
        $statusCommit = StatusCommit::with('statusable')->find($id);

        if (!$statusCommit) {
            return back()->with('error', 'StatusCommit not found.');
        }

        // Ubah nilai status menjadi "preliminary"
        $statusCommit->update([
            'approver_id' => $loggedInUser->id,
            'status' => 'allowed',
            'description' => $request->description,
        ]);
        
        $statusCommit->statusable->presence->update([
            'entry_time' => '08:30'
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