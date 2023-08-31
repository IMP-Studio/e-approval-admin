<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Leave;
use App\Models\StandUp;
use App\Models\Presence;
use App\Models\Telework;
use App\Models\WorkTrip;
use App\Models\LeaveStatus;
use App\Models\Employee;
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

        $user = User::with('employee')->where('email', $request->email)->first();
        $nama_lengkap = $user->employee->first_name .' '. $user->employee->last_name;
    
        $userData = [
            'id' => $user->id,
            'user_id' => $user->employee->user_id,
            'nama_lengkap' => $nama_lengkap,
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
            'password' => $user->password,
            'facepoint' => $user->facePoint,
            'remember_token' => $user->remember_token,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
        if($user!='[]' && Hash::check($request->password, $user->password)){
            if ($user->hasRole('employee')){
                $token=$user->createToken('Personal Acces Token')->plainTextToken;
                $response = [
                    'status' => 200,
                    'token' => $token,
                    'user' => $userData,
                    'message' => 'Successfully Login! Welcome Back.',
                ];
                return response()->json($response);
            } else {
                $response = [
                    'status' => 500,
                    'message' => 'Anda tidak memiliki akses sebagai employees.',
                ];
                return response()->json($response);
            }
        }else if($user=='[]'){
            $response = ['status' => 500, 'message' => 'Akun tidak tersimpan dalam database'];
            return response()->json($response);
        }else{
            $response = ['status' => 500, 'message' => 'Email atau password salah! tolong coba lagi!'];
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
    $systemDate = date('Y-m-d');
    $currentDate = Carbon::now('Asia/Jakarta')->toDateString();

    $attendance = Presence::with('telework','worktrip')
                          ->where('user_id', $id)
                          ->whereDate('date', $currentDate)
                          ->first();

    if (!$attendance) {
        return response()->json(['status' => 'notAttended', 'system_date' => $systemDate, 'carbon_date' => $currentDate]);
    }

    $teleworkStatus = $attendance->telework ? $attendance->telework->status : null;
    $worktripStatus = $attendance->worktrip ? $attendance->worktrip->status : null;

    if (($teleworkStatus && $teleworkStatus == 'pending') || ($worktripStatus && $worktripStatus == 'pending')) {
        return response()->json(['status' => 'pendingStatus', 'message' => 'Your request is still pending. Wait for a moment for a response.', 'system_date' => $systemDate, 'carbon_date' => $currentDate]);
    } elseif (($teleworkStatus && $teleworkStatus == 'rejected') || ($worktripStatus && $worktripStatus == 'rejected')) {
        return response()->json(['status' => 'canReAttend', 'message' => 'You can mark your attendance again', 'system_date' => $systemDate, 'carbon_date' => $currentDate]);
    } elseif ($attendance->exit_time == '00:00:00') {
        return response()->json(['status' => 'checkedIn', 'system_date' => $systemDate, 'carbon_date' => $currentDate, 'attendance_date' => $attendance->date]);
    } else {
        return response()->json(['status' => 'checkedOut', 'system_date' => $systemDate, 'carbon_date' => $currentDate, 'attendance_date' => $attendance->date]);
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
        $userId = $request->query('user_id');
        $user = User::with('employee', 'standups')->where('id', $request->id)->first();
    
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
    
        $teleworkSubQuery = Telework::select('presence_id');
        $worktripSubQuery = Worktrip::select('presence_id');
        $leaveStatusSubQuery = LeaveStatus::select('leave_id');
    
        if (count($statuses) > 0) {
            $teleworkSubQuery->whereIn('status', $statuses);
            $worktripSubQuery->whereIn('status', $statuses);
            $leaveStatusSubQuery->whereIn('status', $statuses);
        }
    
        $leaveSubQuery = Leave::select('presence_id')
            ->whereIn('id', $leaveStatusSubQuery);
    
        $presence = Presence::with('user', 'standup', 'worktrip', 'telework', 'leave')
            ->where('user_id', $request->id)
            ->where(function ($query) use ($teleworkSubQuery, $worktripSubQuery, $leaveSubQuery) {
                $query->whereIn('id', $teleworkSubQuery)
                    ->orWhereIn('id', $worktripSubQuery)
                    ->orWhereIn('id', $leaveSubQuery);
            })
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($presence) {
                $nama_lengkap = $presence->user ? $presence->user->employee->first_name . ' ' . $presence->user->employee->last_name : '';
    
                $data = [
                    'id' => $presence->id,
                    'user_id' => $presence->user_id,
                    'nama_lengkap' => $nama_lengkap,
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
                    $data['status'] = $presence->telework->status;
                    $data['description'] = $presence->telework->description;
                } elseif ($presence->category === 'work_trip') {
                    $data['file'] = $presence->worktrip->file;
                    $data['start_date'] = $presence->worktrip->start_date;
                    $data['end_date'] = $presence->worktrip->end_date;
                    $data['status'] = $presence->worktrip->status;
                    $data['description'] = $presence->worktrip->description;
                } elseif ($presence->category === 'leave') {
                    $relevantLeave = $presence->leave ?? Leave::where('user_id', $presence->user_id)
                        ->where('start_date', '<=', $presence->date)
                        ->where('end_date', '>=', $presence->date)
                        ->first();
                
                    if ($relevantLeave) {
                        $data['type'] = $relevantLeave->type;
                        $data['submission_date'] = $relevantLeave->submission_date;
                        $data['total_leave_days'] = $relevantLeave->total_leave_days;
                        $data['start_date'] = $relevantLeave->start_date;
                        $data['end_date'] = $relevantLeave->end_date;
                        $data['entry_date'] = $relevantLeave->entry_date;
                        $data['status'] = $relevantLeave->leavestatus->status;
                        $data['description'] = $relevantLeave->description;
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
    
                return $data;
            });
    
        if ($presence->isEmpty()) {
            return response()->json(['message' => 'Belum presence atau status presence belum diterima']);
        }
    
        return response()->json(['message' => 'Success', 'data' => $presence]);
    }
    
    
    

    //FUNCTION STORE PRESENCE

    public function storePresence(Request $request) {
        $gambarBinary = base64_encode($request->input('face_point'));
        
        $temporaryEntryTime = now();  
        $userId = $request->input('user_id');

        $user = User::with('employee','standups')->where('id',$userId )->first();
    
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    

        if (!$user->hasRole('employee')){
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
                WorkTrip::create([
                    'user_id' => $request->input('user_id'),
                    'presence_id' => $presence->id,
                    'file' => $request->input('file'),
                    'reject_description' => $request->input('description'),
                    'start_date' => $request->input('start_date'),
                    'end_date' => $request->input('end_date'),
                    'status' => 'pending'
                ]);
                break;
    
            case 'telework':
                Telework::create([
                    'user_id' => $request->input('user_id'),
                    'presence_id' => $presence->id,
                    'telework_category' => $request->input('telework_category'),
                    'category_description' => $request->input('category_description'),
                    'face_point' => $request->input('face_point'),
                    'status' => 'pending',
                    'reject_description' => $request->input('description')
                ]);
                break;
        }
    

        $user->facePoint = $request->input('face_point');
        $user->save();
    
        return response()->json(['message' => 'Success', 'data' => $presence, 'user' => $user]);
    }
    

    //FUNCTION UPDATE PRESENCE //BISA

    public function updatePresence(Request $request, $id) {
        $updateabsensi = Presence::with('user', 'standup', 'telework','worktrip')->find($id);
    
        if (!$updateabsensi) {
            return response()->json(['message' => 'Record not found'], 404);
        }
    
        $updateabsensi->update($request->all());
    
        switch ($updateabsensi->category) {
            case 'work_trip':
                $worktrip = WorkTrip::where('presence_id', $updateabsensi->id)->first();
                if ($worktrip) {
                    if ($request->input('status')) {
                        $worktrip->status = $request->input('status');
                    }
                    if ($request->input('status') === 'rejected' && $request->input('description')) {
                        $worktrip->description = $request->input('description');
                    }
                    $worktrip->save();
                }
                break;
    
            case 'telework':
                $telework = Telework::where('presence_id', $updateabsensi->id)->first();
                if ($telework) {
                    if ($request->input('status')) {
                        $telework->status = $request->input('status');
                    }
                    if ($request->input('status') === 'rejected' && $request->input('description')) {
                        $telework->description = $request->input('description');
                    }
                    $telework->save();
                }
                break;
        }
    
        if ($request->input('status') === 'rejected' && !$request->input('description')) {
            return response()->json(['message' => 'Description is required when rejecting.'], 400);
        }
    
        if (in_array($updateabsensi->category, ['work_trip', 'telework'])) {
            if ($request->input('status') === 'allowed' && $updateabsensi->entry_time === '00:00:00') {
                $updateabsensi->entry_time = $updateabsensi -> temporary_entry_time;
                $updateabsensi->save();
            }
        }        
    
        return response()->json(['message' => 'Berhasil', 'data' => $updateabsensi]);
    }

    //FUNCTION DELETE PRESENCE (TELEWORK) //BISA
    
    public function destroyTelework(Request $request, $id) {
        $telework = Telework::find($id);
    
        if (!$telework) {
            return response()->json(['message' => 'Telework record not found'], 404);
        }
    
        $presenceId = $telework->presence_id;
    
        // Delete the telework record
        $telework->delete();
    
        // Now, delete the related presence record
        if ($presenceId) {
            Presence::destroy($presenceId);
        }
    
        return response()->json(['message' => 'Telework and related presence records deleted successfully']);
    }

    //FUNCTION DELETE PRESENCE (WORKTRIP) //BISA
    public function destroyWorktrip(Request $request, $id) {
        $worktrip = WorkTrip::find($id);
    
        if (!$worktrip) {
            return response()->json(['message' => 'Worktrip record not found'], 404);
        }
    
        $presenceId = $worktrip->presence_id;
    
        // Delete the worktrip record
        $worktrip->delete();
    
        // Now, delete the related presence record
        if ($presenceId) {
            Presence::destroy($presenceId);
        }
    
        return response()->json(['message' => 'Worktrip and related presence records deleted successfully']);
    }

    //FUNCTION DELETE PRESENCE (WFO) //BISA
    public function destroyWFO(Request $request, $id) {
        $wfoPresence = Presence::where('id', $id)->where('category', 'WFO')->first();
    
        if (!$wfoPresence) {
            return response()->json(['message' => 'WFO presence record not found'], 404);
        }
    
        // Delete the WFO presence record
        $wfoPresence->delete();
    
        return response()->json(['message' => 'WFO presence record deleted successfully']);
    }
    
    

    //---- STAND UP FUNCTION ----\\

    //FUNCTION GET STAND UP //BISA

    public function getStandUp(Request $request){
        $user = User::with('employee','standups')->where('id', $request->id)->first();
        if ($user->hasRole('employee')){
            $query = $request->has('id') ? StandUp::with('user','project','presence')->where('user_id', $request->id) : StandUp::with('user','project','presence');
    
            $standUps = $query->orderBy('updated_at', 'desc')->get()
            ->map(function ($standUp) {
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
        } else {
           $response = [
               'status' => 500,
               'message' => 'Anda tidak memiliki akses sebagai employees.',
           ];
           
        }
    }   

    //FUNCTION STORE STAND UP //BISA

    public function storeStandUp(Request $request) {
        $latestPresence = Presence::where('user_id', $request->input('user_id'))->latest('created_at')->first();

        if (!$latestPresence) {
            return response()->json(['message' => 'Failed. No presence record found for the user.'], 400);
        }

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
    public function getLeave(Request $request){
        $userId = $request->query('id');
       
        $jenisleave = $request->query('type');

        $leaveQuery = Leave::with('user','presence','leavestatus')->orderBy('updated_at', 'desc');
        
        if ($userId) {
            $leaveQuery = $leaveQuery->where('user_id', $userId);
        }
    
        if ($jenisleave) {
            $leaveQuery = $leaveQuery->where('type', $jenisleave);
        }
       
            $leave = $leaveQuery->get()->map(function ($leave) {
                if ($leave->user) {
                    $nama_lengkap = $leave->user->employee->first_name .' '. $leave->user->employee->last_name;
                }
        
                $leaveStatus = $leave->leavestatus ? $leave->leavestatus->status : null;
                $rejectDescription = ($leaveStatus === 'rejected' && $leave->leavestatus) ? $leave->leavestatus->description : null;
        
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
                    'description' => $leave->description,
                    'status' => $leaveStatus,
                    'reject_description' => $rejectDescription, 
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
            'description' => $request->input('description'),
        ]);
    
        $leavestatus = LeaveStatus::create([
            'user_id' => $leave->user_id,
            'leave_id' => $leave->id,
            'status' => 'pending', 
            'description' => null, 
        ]);
    
        return response()->json(['message' => 'Success', 'data' => $leave]);
    }
    
    
    
    
    
    
    // FUNCTION UPDATE LEAVE //BISA 
    public function updateLeave(Request $request, $id) 
{
    $leave = Leave::with('leavestatus')->find($id);

    if ($leave) {
        $leave->update($request->all());

        if ($request->has('status')) {
            $statusData = [
                'status' => $request->input('status'),
                'description' => $request->input('description', null)
            ];

            if ($leave->leavestatus) {
                $leave->leavestatus->update($statusData);
            } else {
                $leave->leavestatus()->create($statusData);
            }

            
            if ($request->input('status') === 'allowed') {
                $tanggalMulai = Carbon::parse($leave->start_date);
                $tanggalAkhir = Carbon::parse($leave->end_date);

                while ($tanggalMulai->lte($tanggalAkhir)) {
                    Presence::create([
                        'user_id' => $leave->user_id,
                        'category' => 'leave',
                        'entry_time' => '08:30:00',
                        'exit_time' => '17:30:00',
                        'date' => $tanggalMulai->toDateString(),
                    ]);
                    $tanggalMulai->addDay();
                }
            }
        }

        return response()->json(['message' => 'Update successful', 'data' => $leave]);
    } else {
        return response()->json(['message' => 'Record not found'], 404);
    }
}


    //FUNCTION DELETE LEAVE
    public function destroyLeave($id) 
    {
        $cuti = Leave::with('leavestatus')->find($id);

        if ($cuti) {
            if ($cuti->leavestatus) {
                $cuti->leavestatus->delete();
            }

            $cuti->delete();

            return response()->json(['message' => 'Delete successful']);
        } else {
            return response()->json(['message' => 'Record not found'], 404);
        }
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
