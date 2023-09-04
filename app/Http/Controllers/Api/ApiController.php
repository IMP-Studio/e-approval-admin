<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Leave;
use App\Models\Partner;
use App\Models\Project;
use App\Models\StandUp;
use App\Models\Employee;
use App\Models\Presence;
use App\Models\Telework;
use App\Models\WorkTrip;
use App\Models\LeaveStatus;
use App\Models\StatusCommit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class ApiController extends Controller
{
    
    public function loginApi(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['status' => 500, 'message' => 'Akun tidak tersimpan dalam database']);
    }

    if ($user->hasRole('super-admin')) {
        return response()->json(['status' => 500, 'message' => 'SuperAdmin cannot log in.']);
    }

    // Load employee relation if user has the role 'employee'
    if ($user->hasRole('employee')) {
        $user->load('employee');
    }

    if (!$user->employee) {
        return response()->json(['status' => 500, 'message' => 'Employee data not found.']);
    }
    \Log::info('User permissions: ' . json_encode($user->getPermissionNames()));



    $userData = [
        'id' => $user->id,
        'user_id' => $user->employee->user_id,
        'nama_lengkap' => $nama_lengkap = $user->employee->first_name .' '. $user->employee->last_name,
        'divisi' => $user->employee->division->name,
        'posisi' => $user->employee->position->name,
        'avatar' => $user->employee->avatar,
        'id_number' => $user->employee->id_number,
        'gender' => $user->employee->gender,
        'address' => $user->employee->address,
        'birth_date' => $user->employee->birth_date,
        'is_active' => $user->employee->is_active,
        'name' => $user->name,
        'email' => $user->email,
        'email_verified_at' => $user->email_verified_at,
        'role' => $user->getRoleNames()->first(),
        'permission' => $user->getPermissionNames()->first(),
        'password' => $user->password,
        'facepoint' => $user->facePoint,
        'remember_token' => $user->remember_token,
        'created_at' => $user->created_at,
        'updated_at' => $user->updated_at,
    ];

    $permissionName = $user->getPermissionNames()->first();

    

    if($user!='[]' && Hash::check($request->password, $user->password)){
        if ($user->hasRole('employee')) {
                if ($permissionName == 'ordinary_employee') {
                    $token=$user->createToken('Personal Acces Token')->plainTextToken;
                    $response = [
                        'status' => 200,
                        'token' => $token,
                        'user' => $userData,
                        'message' => 'Successfully Login! Welcome Back Employee',
                    ];
                    return response()->json($response);
                } elseif ($permissionName == 'head_of_tribe') {
                    $token=$user->createToken('Personal Acces Token')->plainTextToken;
                    $response = [
                        'status' => 200,
                        'token' => $token,
                        'user' => $userData,
                        'message' => 'Successfully Login! Welcome Back Head of Tribe',
                    ];
                    return response()->json($response);
                } elseif ($permissionName == 'human_resource') {
                    $token=$user->createToken('Personal Acces Token')->plainTextToken;
                    $response = [
                        'status' => 200,
                        'token' => $token,
                        'user' => $userData,
                        'message' => 'Successfully Login! Welcome Back Human Resource',
                    ];
                    return response()->json($response);
                } elseif ($permissionName == 'president') {
                    $token=$user->createToken('Personal Acces Token')->plainTextToken;
                    $response = [
                        'status' => 200,
                        'token' => $token,
                        'user' => $userData,
                        'message' => 'Successfully Login! Welcome Back President',
                    ];
                    return response()->json($response);
                }
        }
    }else{
        $response = [
            'status' => 500,
            'message' => 'Please enter a valid data',
        ];
        return response()->json($response);

    }

}


    public function fetchFacePoint(Request $request) {
        $user = User::with('employee')->where('id', $request->id)->first();

        if ($user && $user->employee) {
            $nama_lengkap = $user->employee->first_name .' '. $user->employee->last_name;
    
            $response = [
                'name' => $nama_lengkap,
                'facePoint' => json_decode($user->facePoint, true)
            ];
    
            return response()->json($response);
        } else {
            return response()->json(['message' => 'Face point employee not found'], 404);
        }
    }

    
    
    
    //---- PRESENCE FUNCTION ----\\ 

    //FUNCTION PRESENCE TODAY STATUS //BISA
    public function presenceToday($id)
{
    $currentDate = Carbon::now('Asia/Jakarta')->toDateString();

    $attendance = Presence::with(['telework.statusCommit', 'worktrip.statusCommit', 'leave.statusCommit'])
                          ->where('user_id', $id)
                          ->whereDate('date', $currentDate)
                          ->first();

    if (!$attendance) {
        return response()->json(['status' => 'notAttended', 'carbon_date' => $currentDate]);
    }

    $teleworkStatus = $attendance->telework ? $attendance->telework->statusCommit->sortByDesc('created_at')->first()->status : null;
    $worktripStatus = $attendance->worktrip ? $attendance->worktrip->statusCommit->sortByDesc('created_at')->first()->status : null;
    $leaveStatus = $attendance->leave ? $attendance->leave->statusCommit->sortByDesc('created_at')->first()->status : null;

    if (($teleworkStatus && $teleworkStatus == 'pending') || ($worktripStatus && $worktripStatus == 'pending') || ($leaveStatus && $leaveStatus == 'pending')) {
        return response()->json(['status' => 'pendingStatus', 'message' => 'Your request is still pending. Wait for a moment for a response.', 'carbon_date' => $currentDate]);
    } elseif (($teleworkStatus && $teleworkStatus == 'rejected') || ($worktripStatus && $worktripStatus == 'rejected') || ($leaveStatus && $leaveStatus == 'rejected')) {
        return response()->json(['status' => 'canReAttend', 'message' => 'You can mark your attendance again', 'carbon_date' => $currentDate]);
    } elseif ($attendance->exit_time == '00:00:00') {
        return response()->json(['status' => 'checkedIn', 'carbon_date' => $currentDate, 'attendance_date' => $attendance->date]);
    } else {
        return response()->json(['status' => 'checkedOut', 'carbon_date' => $currentDate, 'attendance_date' => $attendance->date]);
    }
}


    

    //FUNCTION GET PRESENCE TODAY //BISAA
    public function getPresenceToday($id) {
        $currentDate = Carbon::now('Asia/Jakarta')->toDateString();
    
        $attendanceToday = Presence::where('user_id', $id)
                                  ->whereDate('date', $currentDate)
                                  ->first();
    
        if ($attendanceToday) {
            return response()->json([
                'status' => 'attended',
                'category' => $attendanceToday->category,
                'entry_time'     => $attendanceToday->entry_time,
                'exit_time'    => $attendanceToday->exit_time,
                'date'         => $attendanceToday->date
            ]);
        } else {
            // Return more detailed information for debugging:
            return response()->json([
                'status' => 'notAttended',
                'system_date' => $currentDate,
                'carbon_date' => $currentDate,
                'user_id' => $id  // Confirming the user_id used in the query
            ]);
        }
    }

    //FUNCTION GET PRESENCE //BISA 

    public function getPresence(Request $request) {
        $loggedInUserId = $request->id;
        $scope = $request->query('scope');

    
        $user = User::with('employee', 'standups')->where('id', $loggedInUserId)->first();
        $permissionName = $user->getPermissionNames()->first();
    
        if (!$user || !$user->hasRole('employee')) {
            return response()->json([
                'status' => 500,
                'message' => 'Anda tidak memiliki akses sebagai employees.'
            ]);
        }
    
        $statuses = [];
        if ($request->has('status') && $request->status != 'all') {
            $statuses = explode(',', $request->status);
        }
    
        $presenceQuery = Presence::with(['user', 'standup', 'worktrip', 'telework', 'leave']);
    
        if ($permissionName == 'ordinary_employee' || $scope === 'self') {
            $presenceQuery->where('user_id', $loggedInUserId);
        }
    
        if (count($statuses) > 0) {
            $presenceQuery->where(function ($query) use ($statuses) {
                $query->whereHas('telework.statusCommit', function ($q) use ($statuses) {
                    $q->whereIn('status', $statuses);
                })
                ->orWhereHas('worktrip.statusCommit', function ($q) use ($statuses) {
                    $q->whereIn('status', $statuses);
                })
                ->orWhereHas('leave.statusCommit', function ($q) use ($statuses) {
                    $q->whereIn('status', $statuses);
                });
                if (in_array('allowed', $statuses)) {
                    $query->orWhere('category', 'WFO');
                }
            });
        }

        function getLevelDescription($level) {
            switch ($level) {
                case 1  :
                    return 'Head of Tribe';
                case 2:
                    return 'Human Resource';
                case 3:
                    return 'President';
            }
        }
    
        $presence = $presenceQuery->orderBy('updated_at', 'desc')
        ->get()
        ->map(function ($presence) {
                $nama_lengkap = $presence->user ? $presence->user->employee->first_name . ' ' . $presence->user->employee->last_name : '';
                
                
                $data = [
                    'id' => $presence->id,
                    'user_id' => $presence->user_id,
                    'nama_lengkap' => $nama_lengkap,
                    'posisi' => $presence->user->employee->position->name,
                    'category' => $presence->category,
                    'entry_time' => $presence->entry_time,
                    'exit_time' => $presence->exit_time,
                    'date' => $presence->date,
                    'latitude' => $presence->latitude,
                    'longitude' => $presence->longitude,
                    'created_at' => $presence->created_at,
                    'updated_at' => $presence->updated_at,
                ];
    
                if ($presence->category === 'telework') {
                    $data['category_description'] = $presence->telework->category_description;
                    $data['telework_category'] = $presence->telework->telework_category;
                    $data['face_point'] = $presence->telework->face_point;
                    if ($presence->telework) {
                        $mostRecentStatus = $presence->telework->statusCommit->sortByDesc('created_at')->first();
                    
                        if ($mostRecentStatus && in_array($mostRecentStatus->status, ['allowed', 'rejected', 'allow_HT'])) {
                            $approver = $mostRecentStatus->approver;
                            $approverPermission = $approver->getPermissionNames()->first();
                        
                            if ($approver && in_array($approverPermission, ['head_of_tribe','human_resource','president'])) {
                                $data['approver_id'] = $approver->id;
                                $data['approver_name'] = $approver->employee->first_name . ' ' . $approver->employee->last_name;
                                $data['permission_approver'] = $approverPermission;
                            }
                        }
                        
                    }
                } elseif ($presence->category === 'work_trip') {
                    $data['file'] = $presence->worktrip->file;
                    $data['start_date'] = $presence->worktrip->start_date;
                    $data['end_date'] = $presence->worktrip->end_date;
                    $data['entry_date'] = $presence->worktrip->entry_date;
                    if ($presence->worktrip) {
                        $mostRecentStatus = $presence->worktrip->statusCommit->sortByDesc('created_at')->first();
                    
                        if ($mostRecentStatus && in_array($mostRecentStatus->status, ['allowed', 'rejected', 'allow_HT'])) {
                            $approver = $mostRecentStatus->approver;
                            $approverPermission = $approver->getPermissionNames()->first();
                        
                            if ($approver && in_array($approverPermission, ['head_of_tribe','human_resource','president'])) {
                                $data['approver_id'] = $approver->id;
                                $data['approver_name'] = $approver->employee->first_name . ' ' . $approver->employee->last_name;
                                $data['permission_approver'] = $approverPermission;
                            }
                        }
                        
                    }
                    
                } elseif ($presence->category === 'leave') {
                    $relevantLeave = $presence->leave ?? Leave::where('user_id', $presence->user_id)
                        ->where('start_date', '<=', $presence->date)
                        ->where('end_date', '>=', $presence->date)
                        ->first();
            
                    if ($relevantLeave) {
                        $data['type'] = $relevantLeave->type;
                        $data['type_description'] = $relevantLeave->type_description;
                        $data['submission_date'] = $relevantLeave->submission_date;
                        $data['total_leave_days'] = $relevantLeave->total_leave_days;
                        $data['start_date'] = $relevantLeave->start_date;
                        $data['end_date'] = $relevantLeave->end_date;
                        $data['entry_date'] = $relevantLeave->entry_date;

                        if ($presence->leave) {
                            $mostRecentStatus = $presence->leave->statusCommit->sortByDesc('created_at')->first();
                            if ($mostRecentStatus && in_array($mostRecentStatus->status, ['allowed', 'rejected', 'allow_HT'])) {
                                $approver = $mostRecentStatus->approver;
                                $approverPermission = $approver->getPermissionNames()->first();
                            
                                if ($approver && in_array($approverPermission, ['head_of_tribe','human_resource','president'])) {
                                    $data['approver_id'] = $approver->id;
                                    $data['approver_name'] = $approver->employee->first_name . ' ' . $approver->employee->last_name;
                                    $data['permission_approver'] = $approverPermission;
                                }
                            }
                            
                        }
                    }
                }
                
        
                if ($presence->standup) {  
                    $data['standup_id'] = $presence->standup->id;
                    $data['done'] = $presence->standup->done;
                    $data['doing'] = $presence->standup->doing;
                    $data['blocker'] = $presence->standup->blocker;
                    $data['project'] = $presence->standup->project_id;
                    $data['project_name'] = $presence->standup->project->name;
                    $data['partner'] = $presence->standup->project->partnername;
                }

            
                if (isset($mostRecentStatus) && $mostRecentStatus) {
                    $data['status'] = $mostRecentStatus->status;
                    $data['status_description'] = $mostRecentStatus->description;
                }
    
                return $data;
            });
    
        if ($presence->isEmpty()) {
            return response()->json(['message' => 'Belum presence atau status presence belum diterima']);
        }
    
        return response()->json(['message' => 'Success', 'data' => $presence]);
    }
    

    //FUNCTION STORE PRESENCE . dicoba lagi.. kan baru

    public function storePresence(Request $request) {
        $gambarBinary = base64_encode($request->input('face_point'));
    
        $temporaryEntryTime = now();  
        $userId = $request->input('user_id');
    
        $user = User::with('employee', 'standups')->where('id', $userId)->first();
    
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        if (!$user->hasRole('employee')) {
            return response()->json([
                'status' => 500,
                'message' => 'Anda tidak memiliki akses sebagai employees.',
            ]);
        }
    
        $entryTime = '00:00:00';  
        if ($request->input('category') === 'WFO') {
            $entryTime = now()->format('H:i:s');  
        }
    
        $presence = Presence::create([
            'user_id' => $request->input('user_id'),
            'category' => $request->input('category'),
            'entry_time' => $entryTime,
            'exit_time' => '00:00:00', 
            'temporary_entry_time' => $temporaryEntryTime,
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'date' => now(),
        ]);
    
        switch ($request->input('category')) {
            case 'work_trip':
                $workTrip = WorkTrip::create([
                    'user_id' => $request->input('user_id'),
                    'presence_id' => $presence->id,
                    'file' => $request->input('file'),
                    'reject_description' => $request->input('description'),
                    'start_date' => $request->input('start_date'),
                    'end_date' => $request->input('end_date'),
                    'face_point' => $request->input('face_point'),
                    'entry_date' => $request->input('end_date'),
                ]);
                $workTrip->statusCommit()->create([
                    'status' => 'pending'
                ]);
                break;
    
            case 'telework':
                $telework = Telework::create([
                    'user_id' => $request->input('user_id'),
                    'presence_id' => $presence->id,
                    'telework_category' => $request->input('telework_category'),
                    'category_description' => $request->input('category_description'),
                    'face_point' => $request->input('face_point'),
                    'reject_description' => $request->input('description')
                ]);
                $telework->statusCommit()->create([
                    'status' => 'pending'
                ]);
                break;
        }
    
        $user->facePoint = $request->input('face_point');
        $user->save();
    
        return response()->json(['message' => 'Success', 'data' => $presence, 'user' => $user]);
    }
    
    

    //FUNCTION UPDATE PRESENCE //BISA

    public function updatePresence(Request $request, $id) {
        $errors = [];
    
        if (!$request->has('status') || !in_array($request->input('status'), ['rejected', 'allowed', 'allow_HT'])) {
            $errors['status'] = 'The status field is required and must be one of: rejected, allowed, allow_HT.';
        }
    
        if ($request->has('description') && !is_string($request->input('description'))) {
            $errors['description'] = 'The description must be a string.';
        }
    
        if (in_array($request->input('status'), ['rejected', 'allowed']) && !$request->has('description')) {
            $errors['description'] = 'The description is required when status is rejected or allowed.';
        }
    
        if (!$request->has('approver_id') || !User::find($request->input('approver_id'))) {
            $errors['approver_id'] = 'The approver id field is required and must exist in the users table.';
        }
    
        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 400);
        }
    
        $updateabsensi = Presence::with('user', 'standup', 'telework', 'worktrip')->find($id);
        if (!$updateabsensi) {
            return response()->json(['message' => 'Record not found'], 404);
        }
    
        $updateabsensi->update($request->only(['status', 'description', 'approver_id']));
    
        $categoryUpdateMap = [
            'work_trip' => WorkTrip::class,
            'telework' => Telework::class,
        ];
    
        if (array_key_exists($updateabsensi->category, $categoryUpdateMap)) {
            $modelClass = $categoryUpdateMap[$updateabsensi->category];
            $modelInstance = $modelClass::where('presence_id', $updateabsensi->id)->first();
            if ($modelInstance) {
                $latestStatusCommit = $modelInstance->statusCommit->sortByDesc('created_at')->first();
                if ($latestStatusCommit) {
                    $latestStatusCommit->update($request->only(['status', 'description', 'approver_id']));
                } else {
                    $modelInstance->statusCommit()->create($request->only(['status', 'description', 'approver_id']));
                }
            }
        }
    
        if (in_array($updateabsensi->category, ['work_trip', 'telework']) && 
            $request->input('status') === 'allowed' && 
            $updateabsensi->entry_time === '00:00:00') {
            $updateabsensi->entry_time = $updateabsensi->temporary_entry_time;
            $updateabsensi->save();
        }
    
        return response()->json([
            'message' => 'Berhasil', 
            'data' => $updateabsensi, 
            'status' => $request->input('status')
        ]);
    }
    
    
    

    public function destroyPresence(Request $request, $id) {
        $presence = Presence::find($id);
    
        if (!$presence) {
            return response()->json(['message' => 'Presence record not found'], 404);
        }
    
        switch ($presence->category) {
            case 'telework':
                $telework = Telework::where('presence_id', $id)->first();
                if ($telework) {
                    $telework->statusCommit()->delete();
                    $telework->delete();
                }
                break;
    
            case 'work_trip':
                $worktrip = WorkTrip::where('presence_id', $id)->first();
                if ($worktrip) {
                    $worktrip->statusCommit()->delete();
                    $worktrip->delete();
                }
                break;
    
            case 'WFO':

                break;
    
            default:
                return response()->json(['message' => 'Presence category not recognized'], 400);
        }
    
        $presence->delete();
        
        return response()->json(['message' => 'Presence and related records deleted successfully']);
    }
    
    
    

    //---- STAND UP FUNCTION ----\\

    //FUNCTION GET STAND UP //BISA

    public function getStandUp(Request $request){
        $user = User::with('employee','standups')->where('id', $request->id)->first();
        
        if (!$user || !$user->hasRole('employee')) {
            return response()->json([
                'status' => 500,
                'message' => 'Anda tidak memiliki akses sebagai employees.',
            ]);
        }
        
        $query = $request->has('id') ? StandUp::with('user','project','presence')->where('user_id', $request->id) : StandUp::with('user','project','presence');
        
        $standUps = $query->orderBy('updated_at', 'desc')->get()
        ->map(function ($standUp) {
            $nama_lengkap = '';
            if ($standUp->user && $standUp->user->employee) {
                $nama_lengkap = $standUp->user->employee->first_name .' '. $standUp->user->employee->last_name;
            }
            return [
                'id' => $standUp->id,
                'user_id' => $standUp->user_id,
                'nama_lengkap' => $nama_lengkap,
                'prensence_id' => $standUp->presence_id,
                'prensence_category' => $standUp->presence->category,
                'project_id' => $standUp->project_id,
                'project' => $standUp->project->name,
                'partner' => $standUp->project->partner->name,
                'done' => $standUp->done,
                'doing' => $standUp->doing,
                'blocker' => $standUp->blocker,
                'created_at' => $standUp->created_at,
                'updated_at' => $standUp->updated_at,
            ];
        });
    
        if ($standUps->isEmpty()) {
            return response()->json(['message' => 'Belum stand up']);
        } else {
            return response()->json(['message' => 'Success', 'data' => $standUps]);
        }
    }

    public function getProject(Request $request){

        $project = Project::with('partner')->orderBy('updated_at', 'desc')->get()
        ->map(function ($project) {
            return [
                'id' => $project->id,
                'partner_id' => $project->partner_id,
                'project' => $project->name,
                'partner' => $project->partner->name,
                'created_at' => $project->created_at,
                'updated_at' => $project->updated_at,
            ];
        });

        if ($project->isEmpty()) {
            return response()->json(['message' => 'Belum ada project']);
        } else {
            return response()->json(['message' => 'Success', 'data' => $project]);
        }

    }
    

    //FUNCTION STORE STAND UP //BISA

    public function storeStandUp(Request $request) {
        if (!$request->has('user_id')) {
            return response()->json(['message' => 'Failed. user_id is not provided in the request.'], 400);
        }
    
        \Log::info('User ID from Request: ' . $request->input('user_id'));
    
        $latestPresence = Presence::where('user_id', $request->input('user_id'))->latest('created_at')->first();
    
        if (!$latestPresence) {
            return response()->json(['message' => 'Failed. No presence record found for the user.'], 400);
        }
    
        \Log::info('Latest Presence ID: ' . $latestPresence->id);
    
        $standup = StandUp::create([
            'user_id' => $request->input('user_id'),
            'done' => $request->input('done'),
            'doing' => $request->input('doing'),
            'blocker' => $request->input('blocker'),
            'presence_id' => $latestPresence->id,
            'project_id' => $request->input('project_id'),
        ]);
    
        return response()->json(['message' => 'Success', 'data' => $standup]);
    }
    
    
    //FUNCTION UPDATE STAND UP //BISAA

    public function updateStandUp(Request $request, $id) {
        $updatestand = StandUp::find($id);
    
        if ($updatestand) {
            $updatestand->update($request->all());
            return response()->json(['message' => 'Berhasil', 'data' => $updatestand]);
        } else {
            return response()->json(['message' => 'Record not found'], 404);
        }
    }

    //FUNCTION DELETE STAND UP //BISA

    public function destroyStandUp($id) 
    {
        $standUp = StandUp::find($id);
        $standUp->delete();
        return response()->json(['message' => 'Data berhasil dihapus', 'data' => $standUp]);
    }

    //---- LEAVE FUNCTION ----\\

    // GET LEAVE FUNCTION //BISA
    public function getLeave(Request $request) {
        $userId = $request->query('id');
        $jenisleave = $request->query('type');
    
        $leaveQuery = Leave::with(['user', 'presence', 'statusCommit'])->orderBy('updated_at', 'desc');
    
        if ($userId) {
            $leaveQuery = $leaveQuery->where('user_id', $userId);
        }
    
        if ($jenisleave) {
            $leaveQuery = $leaveQuery->where('type', $jenisleave);
        }
    
        $leave = $leaveQuery->get()->map(function ($leave) {
            $nama_lengkap = ($leave->user && $leave->user->employee) 
                           ? $leave->user->employee->first_name .' '. $leave->user->employee->last_name 
                           : null;
        
            // Consider most recent status due to polymorphic relationship.
            $mostRecentStatus = $leave->statusCommit->sortByDesc('created_at')->first();
            $approver_name = $mostRecentStatus && $mostRecentStatus->approver ? $mostRecentStatus->approver->employee->first_name .' '. $mostRecentStatus->approver->employee->last_name : null;
        
            return [
                'id' => $leave->id,
                'user_id' => $leave->user_id,
                'nama_lengkap' => $nama_lengkap,
                'type' => $leave->type,
                'submission_date' => $leave->submission_date,
                'start_date' => $leave->start_date,
                'end_date' => $leave->end_date,
                'entry_date' => $leave->entry_date,
                'total_leave_days' => $leave->total_leave_days,
                'type_description' => $leave->type_description,
                'status' => $mostRecentStatus ? $mostRecentStatus->status : null,
                'status_description' => $mostRecentStatus ? $mostRecentStatus->description : null, 
                'approver_id' => $mostRecentStatus ? $mostRecentStatus->approver_id : null, 
                'approver_name' => $approver_name, 
                'created_at' => $leave->created_at,
                'updated_at' => $leave->updated_at,
            ];
        });
        
    
        if ($leave->isEmpty()) {
            return response()->json(['message' => 'Data leave kosong']);
        } else {
            return response()->json(['message' => 'Success', 'data' => $leave]);
        }
    }
    
    
    
    // FUNCTION STORE LEAVE //BISA
    public function storeLeave(Request $request) {
    
        $tanggalPemohonan = $request->input('submission_date');
        $tanggalMulai = Carbon::parse($request->input('start_date'));
        $tanggalAkhir = Carbon::parse($request->input('end_date'));
    
        $jumlahHariLeave = $tanggalMulai->diffInDays($tanggalAkhir) + 1;
    
        $leave = Leave::create([
            'user_id' => $request->input('user_id'),
            'type' => $request->input('type'),
            'submission_date' => $tanggalPemohonan,
            'total_leave_days' => $jumlahHariLeave,
            'start_date' => $tanggalMulai,
            'end_date' => $tanggalAkhir,
            'entry_date' => $request->input('entry_date'),
            'type_description' => $request->input('type_description'),
        ]);
    
        if (!$leave->statusCommit()->exists()) {
            $leave->statusCommit()->create([
                'approver_id' => null,
                'status' => 'pending',
                'description' => null,
            ]);
        } else {
        }
    
    
        return response()->json(['message' => 'Success', 'data' => $leave]);
    }
    
    
    public function updateLeave(Request $request, $id) {
        $leave = Leave::with('statusCommit')->find($id);
    
        if (!$leave) {
            return response()->json(['message' => 'Record not found'], 404);
        }
    
        $leave->update($request->all());
    
        if ($request->has('status')) {
            $statusData = [
                'status' => $request->input('status'),
                'description' => $request->input('description', null),
                'approver_id' => $request->input('approver_id'),
            ];
    
            $existingStatus = $leave->statusCommit->first();

            if ($existingStatus) {
                $existingStatus->update($statusData);
            }
            
    
            if ($request->input('status') === 'allowed') {
                $tanggalMulai = Carbon::parse($leave->start_date);
                $tanggalAkhir = Carbon::parse($leave->end_date);
    
                while ($tanggalMulai->lte($tanggalAkhir)) {
                    $presence = Presence::create([
                        'user_id' => $leave->user_id,
                        'category' => 'leave',
                        'entry_time' => '08:30:00',
                        'exit_time' => '17:30:00',
                        'date' => $tanggalMulai->toDateString(),
                    ]);
                    $tanggalMulai->addDay();
                }
                $leave->presence_id = $presence->id;
                $leave->save();
            }
        }
    
        return response()->json(['message' => 'Update successful', 'data' => $leave]);
    }
    
    
    



    //FUNCTION DELETE LEAVE //BISA
    public function destroyLeave($id) 
{
    $leave = Leave::with('statusCommit')->find($id);

    if (!$leave) {
        return response()->json(['message' => 'Record not found'], 404);
    }

    $leave->statusCommit()->delete();

    $leave->delete();

    return response()->json(['message' => 'Delete successful']);
}





    //---- PROFILE FUNCTION ----\\

    public function getProfile(Request $request){
        $user = User::with('employee','standups')->where('id', $request->id)->first();
      
    
        if (!$user) {
            return response()->json([
                'status' => 400,
                'message' => 'User not found.',
            ]);
        }

            $employee = employee::with('user','division','position')->where('user_id', $request->id)->orderBy('updated_at', 'desc')->get()
            ->map(function ($employee) {
                $nama_lengkap = $employee->first_name .' '. $employee->last_name;
    
                return [
                    'id' => $employee->id,
                    'user_id' => $employee->user_id,
                    'nama_lengkap'=> $nama_lengkap,
                    'divisi' => $employee->division->name,
                    'posisi' => $employee->position->name,
                    'avatar' => $employee->avatar,
                    'id_number' => $employee->id_number,
                    'gender' => $employee->gender,
                    'address' => $employee->address,
                    'birth_date' => $employee->birth_date,
                    'is_active' => $employee->is_active,
                    'permission' => $employee->user->getPermissionNames()->first(),
                    'name' => $employee->user->name,
                    'email' => $employee->user->email,
                    'email_verified_at' => $employee->user->email_verified_at,
                    'role' => $employee->user->getRoleNames()->first(),
                    'password' => $employee->user->password,
                    'facepoint' => $employee->user->facePoint,
                    'remember_token' => $employee->user->remember_token,
                    'standup' => $employee->user->standups,
                    'standup_count' => [
                        'done_count' => $employee->user->standups->where('done', true)->count(),
                        'doing_count' => $employee->user->standups->where('doing', true)->count(),
                        'blocker_count' => $employee->user->standups->where('blocker', true)->count(),
                    ],
                    'created_at' => $employee->created_at,
                    'updated_at' => $employee->updated_at,
                ];
            });
    
            if ($employee->isEmpty()) {
                return response()->json(['message' => 'Belum absensi atau status absensi belum diterima']);
            } else {
                return response()->json(['message' => 'Success', 'data' => $employee]);
            }
    }

    public function logout() {
        $user = Auth::user();
        $password = request()->input('password');
    
        // Validate the password
        if(!Hash::check($password, $user->password)) {
            return response()->json(['message' => 'Password is incorrect'], 400);
        }
    
        $token = PersonalAccessToken::findToken(request()->bearerToken());
        if ($token && $token->tokenable_id === $user->id) {
            $token->delete();
            return response()->json(['message' => 'Berhasil keluar'], 200);
        } else {
            return response()->json(['message' => 'Token autentikasi tidak ditemukan'], 400);
        }
    }
    

}
