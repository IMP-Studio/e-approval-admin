<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Leave;
use App\Mail\OTPEmail;
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
use App\Models\OtpVerification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{

      //---- ForgetPassword otp FUNCTION ----\\ 

    //change password..

      public function changePasswordWithoutOtpVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'new_password' => 'required|min:8|confirmed',
        ]);
    
        if ($validator->fails()) {
            $errors = $validator->errors();
            $errorMessages = $errors->all();
            $response = [
                'status' => 400,
                'message' => $errorMessages,
            ];
    
            return response()->json($response, $response['status']);
        }
    
        // Check if the current password is valid
        $isCurrentPasswordValid = $this->validateCurrentPassword($request->user_id, $request->validate);
    
        if (!$isCurrentPasswordValid) {
            $response = [
                'status' => 400,
                'message' => 'Incorrect current password',
            ];
            return response()->json($response, $response['status']);
        }
    
        $user = User::where('id', $request->user_id)->first();
    
        if (!$user) {
            $response = [
                'status' => 400,
                'message' => 'User not found',
            ];
            return response()->json($response, $response['status']);
        }
    
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);
    
        $response = [
            'status' => 200,
            'message' => 'Password changed successfully',
        ];
    
        return response()->json($response, $response['status']);
    }
    
    private function validateCurrentPassword($userId, $enteredPassword)
    {
        $user = User::where('id', $userId)->first();
    
        if (!$user) {
            return false;
        }
    
        return Hash::check($enteredPassword, $user->password);
    }

    // get otp done\\
    public function getOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            $errorMessages = $errors->all();
            $response = [
                'status' => 400,
                'message' => $errorMessages,
            ];

            return response()->json($response, $response['status']);
        }

        $user = User::with('employee')->where('email', $request->email)->first();

        if ($user) {
            OtpVerification::where('user_id', $user->id)->delete();

            $otp = rand(100000, 999999);

            $otpVerification = new OtpVerification([
                'user_id' => $user->id,
                'otp_code' => $otp,
                'expiry_time' => now('Asia/Jakarta')->addMinutes(10),
            ]);
            $otpVerification->save();

            Mail::to($request->email)->send(new OTPEmail($otp));

            $response = [
                'status' => 200,
                'message' => 'OTP sent to email',
                'data' => [
                    'user_id' => $user->id,
                    'otp_code' => $otpVerification->otp_code,
                    'firstname' => $user->employee->firstname,
                    'email' => $request->email,
                ],
            ];
            return response()->json($response);
        } else {
            $response = [
                'status' => 404,
                'message' => 'User not found',
            ];
        }

        return response()->json($response, $response['status']);
    }
    // get otp end\\

    // Verif otp done \\
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'otp_code' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            $errorMessages = $errors->all();
            $response = [
                'status' => 400,
                'message' => $errorMessages,
            ];

            return response()->json($response, $response['status']);
        }

        $user = User::where('id', $request->user_id)->first();

        if (!$user) {
            $response = [
                'status' => 404,
                'message' => 'User not found',
            ];
            return response()->json($response, $response['status']);
        }

        $otpVerification = OtpVerification::where('user_id', $user->id)
            ->where('otp_code', $request->otp_code)
            ->first();

        if ($otpVerification) {
            if ($otpVerification->expiry_time > now('Asia/Jakarta')) {
                $otpVerification->update(['is_verified' => 'yes']);

                $response = [
                    'status' => 200,
                    'data' => [
                        'user_id' => $user->id,
                        'otp_code' => $otpVerification->otp_code,
                    ],
                    'message' => 'OTP successfully verified',
                ];
            } else {
                $otpVerification->delete();
                $response = [
                    'status' => 400,
                    'message' => 'OTP code expired, Try Again',
                ];
            }
        } else {
            $response = [
                'status' => 400,
                'message' => 'Invalid OTP, Try Again',
            ];
        }

        return response()->json($response, $response['status']);
    }
    // Verif otp end \\


    // change password done
    public function changePasswordAfterOtpVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'new_password' => 'required|min:8|confirmed',
            'otp_code' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            $errorMessages = $errors->all();
            $response = [
                'status' => 400,
                'message' => $errorMessages,
            ];

            return response()->json($response, $response['status']);
        }

        $user = User::where('id', $request->user_id)->first();

        if (!$user) {
            $response = [
                'status' => 400,
                'message' => 'User not found',
            ];
            return response()->json($response, $response['status']);
        }

        $otpVerification = OtpVerification::where('user_id', $user->id)
            ->where('is_verified', 'yes')
            ->where('otp_code', $request->otp_code)
            ->first();

        if (!$otpVerification) {
            $response = [
                'status' => 400,
                'message' => 'Invalid OTP or OTP not verified',
            ];
        } else {
            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            $otpVerification->delete();

            $response = [
                'status' => 200,
                'message' => 'Password changed successfully',
            ];
        }

        return response()->json($response, $response['status']);
    }
    // change password done end

    //---- ForgetPassword otp FUNCTION end ----\\ 

    
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
    // info('User permissions: ' . json_encode($user->getPermissionNames()));



    $userData = [
        'id' => $user->id,
        'user_id' => $user->employee->user_id,
        'first_name' => $user->employee->first_name,
        'last_name' => $user->employee->last_name,
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
                    $token = $user->createToken('Personal Acces Token')->plainTextToken;
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

        $category = $attendanceToday->category ?? null;

        if($category != null){
            if($category == 'WFO'){
                 $category = 'Work From Office';
            }elseif($category == 'telework'){
                 $category = 'Work From Anywhere';
            }elseif($category == 'work_trip'){
                 $category = 'Perjalanan Dinas';
            }
        }
        
    
        if ($attendanceToday) {
            return response()->json([
                'status' => 'attended',
                'category' => $category,
                'entry_time'     => $attendanceToday -> entry_time,
                'exit_time'    => $attendanceToday->exit_time,
                'date'         => $attendanceToday->date
            ]);
        } else {
            // Return more detailed information for debugging:
            return response()->json([
                'status' => 'notAttended',
                'category' => 'Belum check in',
                'entry_time' => '00:00 AM',
                'exit_time'    =>'00:00 AM',
                'date'         => $currentDate,
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

        if ($request->has('start_date')) {
            $startDate = Carbon::parse($request->start_date);
            $presenceQuery->whereDate('date', '>=', $startDate);
        }
        if ($request->has('end_date')) {
            $endDate = Carbon::parse($request->end_date);
            $presenceQuery->whereDate('date', '<=', $endDate);
        }

        if ($request->has('type')) {
            $type = $request->type;
            if ($type == 'WFA') {
                $presenceQuery->where('category', 'telework'); 
            } elseif ($type == 'PERJADIN') {
                $presenceQuery->where('category', 'work_trip'); 
            }
        }

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
                        
                            if ($approver) { // Check if approver exists
                                $approverPermission = $approver->getPermissionNames()->first();
                        
                                if ($approverPermission && in_array($approverPermission, ['head_of_tribe','human_resource','president'])) {
                                    $data['approver_id'] = $approver->id;
                                    $data['approver_name'] = $approver->employee->first_name . ' ' . $approver->employee->last_name;
                                    $data['permission_approver'] = $approverPermission;
                                }
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
                        
                            if ($approver) { // Check if approver exists
                                $approverPermission = $approver->getPermissionNames()->first();
                        
                                if ($approverPermission && in_array($approverPermission, ['head_of_tribe','human_resource','president'])) {
                                    $data['approver_id'] = $approver->id;
                                    $data['approver_name'] = $approver->employee->first_name . ' ' . $approver->employee->last_name;
                                    $data['permission_approver'] = $approverPermission;
                                }
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
                            
                                if ($approver) { // Check if approver exists
                                    $approverPermission = $approver->getPermissionNames()->first();
                            
                                    if ($approverPermission && in_array($approverPermission, ['head_of_tribe','human_resource','president'])) {
                                        $data['approver_id'] = $approver->id;
                                        $data['approver_name'] = $approver->employee->first_name . ' ' . $approver->employee->last_name;
                                        $data['permission_approver'] = $approverPermission;
                                    }
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
    

    //GET PRESENCE BY ID FUNCTION //BISA

    public function getPresenceById(Request $request, $id) {
        $presence = Presence::with(['user', 'standup', 'worktrip', 'telework', 'leave'])->find($id);
    
        if (!$presence) {
            return response()->json(['status' => 404, 'message' => 'Presence not found']);
        }
    
        $statuses = [];
        if ($request->has('status') && $request->status != 'all') {
            $statuses = explode(',', $request->status);
        }
    
        if (!empty($statuses) && !in_array($presence->category, $statuses)) {
            return response()->json(['status' => 404, 'message' => 'Presence does not match the given status']);
        }
    
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
    

        if ($presence->category === 'telework' && $presence->telework) {
            $data['category_description'] = $presence->telework->category_description;
            $data['telework_category'] = $presence->telework->telework_category;
            $data['face_point'] = $presence->telework->face_point;
            if ($presence->telework) {
                $mostRecentStatus = $presence->telework->statusCommit->sortByDesc('created_at')->first();
            
                if ($mostRecentStatus && in_array($mostRecentStatus->status, ['allowed', 'rejected', 'allow_HT'])) {
                    $approver = $mostRecentStatus->approver;
                
                    if ($approver) { // Check if approver exists
                        $approverPermission = $approver->getPermissionNames()->first();
                
                        if ($approverPermission && in_array($approverPermission, ['head_of_tribe','human_resource','president'])) {
                            $data['approver_id'] = $approver->id;
                            $data['approver_name'] = $approver->employee->first_name . ' ' . $approver->employee->last_name;
                            $data['permission_approver'] = $approverPermission;
                        }
                    }
                }
                
            }
        } elseif ($presence->category === 'work_trip' && $presence->worktrip) {
            $data['file'] = $presence->worktrip->file;
            $data['start_date'] = $presence->worktrip->start_date;
            $data['end_date'] = $presence->worktrip->end_date;
            $data['entry_date'] = $presence->worktrip->entry_date;
            if ($presence->worktrip) {
                $mostRecentStatus = $presence->worktrip->statusCommit->sortByDesc('created_at')->first();
            
                if ($mostRecentStatus && in_array($mostRecentStatus->status, ['allowed', 'rejected', 'allow_HT'])) {
                    $approver = $mostRecentStatus->approver;
                
                    if ($approver) { // Check if approver exists
                        $approverPermission = $approver->getPermissionNames()->first();
                
                        if ($approverPermission && in_array($approverPermission, ['head_of_tribe','human_resource','president'])) {
                            $data['approver_id'] = $approver->id;
                            $data['approver_name'] = $approver->employee->first_name . ' ' . $approver->employee->last_name;
                            $data['permission_approver'] = $approverPermission;
                        }
                    }
                }
                
            }
        } elseif ($presence->category === 'leave' && $presence->leave) {
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
                            
                                if ($approver) { // Check if approver exists
                                    $approverPermission = $approver->getPermissionNames()->first();
                            
                                    if ($approverPermission && in_array($approverPermission, ['head_of_tribe','human_resource','president'])) {
                                        $data['approver_id'] = $approver->id;
                                        $data['approver_name'] = $approver->employee->first_name . ' ' . $approver->employee->last_name;
                                        $data['permission_approver'] = $approverPermission;
                                    }
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
    
        return response()->json(['message' => 'Success', 'data' => $data]);
    }


    public function getLeaveById(Request $request, $id) {
        $leave = Leave::with(['user', 'user.employee', 'statusCommit'])->find($id);
    
        if (!$leave) {
            return response()->json(['status' => 404, 'message' => 'Leave not found']);
        }
    
        $nama_lengkap = $leave->user ? $leave->user->employee->first_name . ' ' . $leave->user->employee->last_name : '';
        $data = [
            'id' => $leave->id,
            'user_id' => $leave->user_id,
            'nama_lengkap' => $nama_lengkap,
            'posisi' => $leave->user->employee->position->name,
            'type' => $leave->type,
            'type_description' => $leave->type_description,
            'submission_date' => $leave->submission_date,
            'total_leave_days' => $leave->total_leave_days,
            'start_date' => $leave->start_date,
            'end_date' => $leave->end_date,
            'entry_date' => $leave->entry_date,
            'created_at' => $leave->created_at,
            'updated_at' => $leave->updated_at,
        ];
    
        $mostRecentStatus = $leave->statusCommit->sortByDesc('created_at')->first();
        if ($mostRecentStatus && in_array($mostRecentStatus->status, ['allowed', 'rejected', 'allow_HT'])) {
            $approver = $mostRecentStatus->approver;
    
            if ($approver) { // Check if approver exists
                $approverPermission = $approver->getPermissionNames()->first();
    
                if ($approverPermission && in_array($approverPermission, ['head_of_tribe', 'human_resource', 'president'])) {
                    $data['approver_id'] = $approver->id;
                    $data['approver_name'] = $approver->employee->first_name . ' ' . $approver->employee->last_name;
                    $data['permission_approver'] = $approverPermission;
                }
            }
            $data['status'] = $mostRecentStatus->status;
            $data['status_description'] = $mostRecentStatus->description;
        }
    
        return response()->json(['message' => 'Success', 'data' => $data]);
    }
    
    
    

    //FUNCTION STORE PRESENCE . dicoba lagi.. kan baru



    public function storePresence(Request $request) {
        DB::beginTransaction();  
    
        try {
            $userId = $request->input('user_id');
            $user = User::with('employee', 'standups')->where('id', $userId)->first();
    
            if (!$user) {
                throw new \Exception('User not found', 404);
            }
    
            if (!$user->hasRole('employee')) {
                throw new \Exception('Anda tidak memiliki akses sebagai employees.', 500);
            }
    
            $entryTime = ($request->input('category') === 'WFO') ? now()->format('H:i:s') : '00:00:00';
    
            $presence = Presence::create([
                'user_id' => $userId,
                'category' => $request->input('category'),
                'entry_time' => $entryTime,
                'exit_time' => '00:00:00',
                'temporary_entry_time' => now(),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'date' => now(),
            ]);
    
            switch ($request->input('category')) {
                case 'work_trip':
                    if (!$request->hasFile('file')) {
                        throw new \Exception('File is required.', 400);
                    }

                    // Assuming face_point is received as a list from the client
                    $facePointList = $request->input('face_point');

                    // Convert the list to JSON
                    $facePointJson = json_encode($facePointList);

                    // Convert the JSON to Base64
                    $facePointBase64 = base64_encode($facePointJson);
            
                    $file = $request->file('file');
            
                    $originalFilename = $file->getClientOriginalName();
                    
                    $storedFilePath = $file->storeAs('files', $originalFilename, 'public'); // Use original filename to store
            
                    if (!$storedFilePath) {
                        throw new \Exception("Error storing the file.");
                    }
            
                    $workTrip = WorkTrip::create([
                        'user_id' => $userId,
                        'presence_id' => $presence->id,
                        'file' => $storedFilePath, // This will now also be the original filename
                        'start_date' => $request->input('start_date'),
                        'end_date' => $request->input('end_date'),
                        'face_point' => $facePointBase64,
                        'entry_date' => $request->input('entry_date'),
                    ]);
            
                    if (!$workTrip->statusCommit()->create(['status' => 'pending'])) {
                        throw new \Exception("Error creating status for WorkTrip.");
                    }
                    break;
    
                case 'telework':
                       $facePointList = $request->input('face_point');

                       $facePointJson = json_encode($facePointList);
   
                       $facePointBase64 = base64_encode($facePointJson);
                    $telework = Telework::create([
                        'user_id' => $userId,
                        'presence_id' => $presence->id,
                        'telework_category' => $request->input('telework_category'),
                        'category_description' => $request->input('category_description'),
                        'face_point' => $facePointBase64,
                        'reject_description' => $request->input('description')
                    ]);
    
                    if (!$telework->statusCommit()->create(['status' => 'pending'])) {
                        throw new \Exception("Error creating status for Telework.");
                    }
                    break;
            }
    
            $user->facePoint = $request->input('face_point');
            $user->save();
    
            DB::commit();  
    
            return response()->json(['message' => 'Success', 'data' => $presence, 'user' => $user], 200);
    
        } catch (\Exception $e) {
            DB::rollBack();  
    
            return response()->json(['message' => 'Failed: ' . $e->getMessage()], $e->getCode() > 0 ? $e->getCode() : 500);
        }
    }

    //FUNCTION UPDATE PRESENCE // Bisa

    public function updatePresence(Request $request, $id) {
        $updateabsensi = Presence::with('telework', 'worktrip')->find($id);
    
        if (!$updateabsensi) {
            return response()->json(['message' => 'Record not found'], 404);
        }
    
        $updateabsensi->update($request->all());
    
        if ($updateabsensi->category == 'work_trip') {
            $relatedModel = Worktrip::where('presence_id', $updateabsensi->id)->first();
    
            if (!$relatedModel) {
                return response()->json(['message' => 'Related work_trip model not found'], 404);
            }
    
            // Handle the file replacement
            if ($request->hasFile('file')){
                $file = $request->file('file');
                $originalName = $file->getClientOriginalName();
    
                if ($relatedModel->file) {
                    Storage::disk('public')->delete('files/' . $relatedModel->file);
                }
    
                $file->storeAs('files', $originalName, 'public');
                $relatedModel->file = $originalName;
                // $relatedModel->save();
            }
    
            $relatedModel->update($request->except('file'));
        } 
        elseif ($updateabsensi->category == 'telework') {
            $relatedModel = Telework::where('presence_id', $updateabsensi->id)->first();
    
            if (!$relatedModel) {
                return response()->json(['message' => 'Related telework model not found'], 404);
            }
    
            $relatedModel->update($request->all());
        }
    
        return response()->json([
            'message' => 'Data updated successfully',
            'data' => $updateabsensi->refresh(),  
        ]);
    }

    
    
    
    //FUNCTION DESTROY PRESENCE //BISA
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
    

    //FUNCTION CHECK OUT  //BISA

    public function checkOut(Request $request)
{
    $userId = $request->input('user_id');
    $currentDate = Carbon::now()->toDateString();  

    $presence = Presence::where('user_id', $userId)
                            ->where('date', $currentDate)
                            ->first();
    
    if (!$presence) {
        return response()->json(['message' => 'No presence record found for today.'], 404);
    }
    
    $wasUpdated = $presence->update([
        'exit_time' => Carbon::now()->toTimeString()  
    ]);

    if ($wasUpdated) {
        return response()->json(['message' => 'Checked out successfully.', 'data' => $presence], 200);
    } else {
        return response()->json(['message' => 'Failed to check out.'], 500);
    }
}

// APPROVE AND REJECT FUNCTION //BISA
public function approveReject(Request $request, $id)
{
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

    DB::table('status_commits')->where('id', $id)->update([
    'approver_id' => $request->input('approver_id'),
    'status' => $request->input('status'),
    'description' => $request->input('description')
]);

$statusCommit = StatusCommit::findOrFail($id);



    $statusCommit = StatusCommit::with('statusable')->findOrFail($id);
    $statusable = $statusCommit->statusable;

    if ($statusable instanceof Presence) {
        $statusable->update($request->only(['status', 'description', 'approver_id']));
        if (in_array($statusable->category, ['work_trip', 'telework']) && 
            $request->input('status') === 'allowed' && 
            $statusable->entry_time === '00:00:00') {
            $statusable->entry_time = $statusable->temporary_entry_time;
            $statusable->save();
        }

        $categoryUpdateMap = [
            'work_trip' => WorkTrip::class,
            'telework' => Telework::class,
        ];
    
        if (array_key_exists($statusable->category, $categoryUpdateMap)) {
            $modelClass = $categoryUpdateMap[$statusable->category];
            $modelInstance = $modelClass::where('presence_id', $statusable->id)->first();
            if ($modelInstance) {
                $latestStatusCommit = $modelInstance->statusCommit->sortByDesc('created_at')->first();
                if ($latestStatusCommit) {
                    $latestStatusCommit->update($request->only(['status', 'description', 'approver_id']));
                } else {
                    $modelInstance->statusCommit()->create($request->only(['status', 'description', 'approver_id']));
                }
            }
        }
    }

    return response()->json(['message' => 'Approval status saved successfully.', 'data' => $statusCommit->fresh()], 200);
}

    //---- STAND UP FUNCTION ----\\

    //FUNCTION GET STAND UP //BISA

    public function getStandUp(Request $request){
        $today = Carbon::today();
        $lastMonth = Carbon::today()->subMonth();
    
        if ($request->has('id')) {
            $user = User::with('employee', 'standups')->where('id', $request->id)->first();
    
            if (!$user || !$user->hasRole('employee')) {
                return response()->json([
                    'status' => 500,
                    'message' => 'Anda tidak memiliki akses sebagai employees.',
                ]);
            }
    
            $query = StandUp::with('user', 'project', 'presence')
                            ->where('user_id', $request->id)
                            ->whereBetween('created_at', [$lastMonth, $today]);
        } else {
            $query = StandUp::with('user', 'project', 'presence')
                            ->whereDate('created_at', $today);
        }
        $standUps = $query->orderBy('updated_at', 'desc')->get()
        ->map(function ($standUp) {
            $dateTime = new \DateTime($standUp->created_at);
            $jam = $dateTime->format('H:m A');
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
                'jam' => $jam,
                'blocker' => $standUp->blocker,
                'created_at' => $standUp->created_at,
                'updated_at' => $standUp->updated_at,
            ];
        });
    
        if ($standUps->isEmpty()) {
            return response()->json(['message' => 'Belum ada yang stand up']);
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
    
        info('User ID from Request: ' . $request->input('user_id'));
    
        $latestPresence = Presence::where('user_id', $request->input('user_id'))->latest('created_at')->first();
    
        if (!$latestPresence) {
            return response()->json(['message' => 'Failed. No presence record found for the user.'], 400);
        }
    
        info('Latest Presence ID: ' . $latestPresence->id);
    
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
        
            $mostRecentStatus = $leave->statusCommit->sortByDesc('created_at')->first();
            $approver_name = $mostRecentStatus && $mostRecentStatus->approver ? $mostRecentStatus->approver->employee->first_name .' '. $mostRecentStatus->approver->employee->last_name : null;
        
            return [
                'id' => $leave->id,
                'user_id' => $leave->user_id,
                'nama_lengkap' => $nama_lengkap,
                'type' => $leave->type,
                'entry_time' => ($mostRecentStatus && $mostRecentStatus->status == 'allowed') ? $leave->presence->entry_time : null,
                'category' => ($mostRecentStatus && $mostRecentStatus->status == 'allowed') ? $leave->presence->category : null,
                'posisi' => $leave->user->employee->position->name,
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
    $currentDate = Carbon::now();
    $submissionDate = Carbon::parse($request->input('submission_date'));
    $startDate = Carbon::parse($request->input('start_date'));

    // if ($startDate->diffInDays($submissionDate, false) < 2) {
    //     return response()->json(['error' => 'Harus submit start_date dengan selisih 2 hari dengan submission_date'], 400);
    // }

    $endDate = Carbon::parse($request->input('end_date'));
    $totalDays = $startDate->diffInDays($endDate) + 1;

    DB::beginTransaction();  

    try {
        $leave = Leave::create([
            'user_id' => $request->input('user_id'),
            'type' => $request->input('type'),
            'submission_date' => $submissionDate,
            'total_leave_days' => $totalDays,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'entry_date' => $request->input('entry_date'),
            'type_description' => $request->input('type_description'),
        ]);

        if (!$leave->statusCommit()->exists()) {
            $leave->statusCommit()->create([
                'approver_id' => null,
                'status' => 'pending',
                'description' => null,
            ]);
        }

        DB::commit();  // Commit the transaction

        return response()->json(['message' => 'Success', 'data' => $leave]);

    } catch (\Exception $e) {
        DB::rollBack();  // Rollback the transaction

        return response()->json(['message' => 'Failed to create leave: ' . $e->getMessage()], 500);
    }
}

    

    
    
    
    public function updateLeave(Request $request, $id) {
        $leave = Leave::with('statusCommit')->find($id);
    
        if (!$leave) {
            return response()->json(['message' => 'Record not found'], 404);
        }
    
        $today = Carbon::now();
        $startDate = Carbon::parse($request->input('start_date'));
        // $differenceInDays = $today->diffInDays($startDate, false); 

    
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

    public function logout(Request $request) {
        $user = Auth::user();
        $inputpassword = $request->input('validpassword');
        $validpassword = Hash::check($inputpassword, $user->password);

        if (!$validpassword) {
            return response()->json(['message' => 'Password salah'], 400);
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