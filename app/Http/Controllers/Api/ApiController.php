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
use App\Models\LeaveDetail;
use App\Models\LeaveStatus;
use App\Models\TypeOfLeave;
use App\Models\StatusCommit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\OtpVerification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\File;


class ApiController extends Controller
{

    ///----- Send Notification bar ------\\\\\

    public function sendCheckinNotification()
    {
        $onesignalApiKey = 'MGEwNDI0NmMtOWIyMC00YzU5LWI3NDYtNzUxMjFjYjdmZGJj';
        $appId = 'd0249df4-3456-48a0-a492-9c5a7f6a875e';


        // Kirim notifikasi ke OneSignal
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $onesignalApiKey,
            'Content-Type' => 'application/json',
        ])->post('https://onesignal.com/api/v1/notifications', [
            'app_id' => $appId,
            'included_segments' => ['All'],
            'template_id' => "53105407-09f3-45c1-9902-344bc83505bd",
        ]);

        return response()->json(['message' => 'Notification sent']);
    }

    // belum fix
    public function sendCheckOutNotification()
    {
        $onesignalApiKey = 'MGEwNDI0NmMtOWIyMC00YzU5LWI3NDYtNzUxMjFjYjdmZGJj';
        $appId = 'd0249df4-3456-48a0-a492-9c5a7f6a875e';


        // Kirim notifikasi ke OneSignal
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $onesignalApiKey,
            'Content-Type' => 'application/json',
        ])->post('https://onesignal.com/api/v1/notifications', [
            'app_id' => $appId,
            'included_segments' => ['All'],
            'template_id' => "b30d717a-2ab7-459c-8a33-a6a78bfac405",
        ]);

        return response()->json(['message' => 'Notification sent']);
    }
    ///----- Send Notification bar end ------\\\\\


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

    if ($user->hasRole('employee')) {
        $user->load('employee');
    }

    if (!$user->hasPermissionTo('can_access_mobile')) {
        return response()->json(['status' => 500, 'message' => "Kamu tidak punya permission untuk akses mobile"]);
    }

    if (!$user->employee) {
        return response()->json(['status' => 500, 'message' => 'Employee data not found.']);
    }

    $userData = [
        'id' => $user->id,
        'user_id' => $user->employee->user_id,
        'first_name' => $user->employee->first_name,
        'last_name' => $user->employee->last_name,
        'nama_lengkap' => $nama_lengkap = $user->employee->first_name .' '. $user->employee->last_name,
        'divisi' => $user->employee->division->name,
        'divisi_id' => $user->employee->division->id,
        'posisi' => $user->employee->position->name,
        'posisi_id' => $user->employee->position->id,
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
        'permission' => $user->getPermissionNames()->intersect([
            'approve_preliminary', 
            'approve_allowed', 
            'reject_presence', 
            'view_request_pending', 
            'view_request_preliminary', 
            'can_access_mobile'
        ])->values(),
        'password' => $user->password,
        'facepoint' => $user->facePoint,
        'remember_token' => $user->remember_token,
        'created_at' => $user->created_at,
        'updated_at' => $user->updated_at,
    ];

    $permissionName = $user->getPermissionNames()->intersect([
        'approve_preliminary', 
        'approve_allowed', 
        'reject_presence', 
        'view_request_pending', 
        'view_request_preliminary', 
        'can_access_mobile'
    ])->values();

    if($user!='[]' && Hash::check($request->password, $user->password)){
        if ($user->hasRole('employee')) {
                if ($permissionName == 'approve_preliminary') {
                    $token=$user->createToken('Personal Acces Token')->plainTextToken;
                    $response = [
                        'status' => 200,
                        'token' => $token,
                        'user' => $userData,
                        'message' => 'Successfully Login! Welcome Back Head of Tribe',
                    ];
                    return response()->json($response);
                } elseif ($permissionName == 'approve_allowed') {
                    $token=$user->createToken('Personal Acces Token')->plainTextToken;
                    $response = [
                        'status' => 200,
                        'token' => $token,
                        'user' => $userData,
                        'message' => 'Successfully Login! Welcome Back Human Resource',
                    ];
                    return response()->json($response);
                }else{
                    $token=$user->createToken('Personal Acces Token')->plainTextToken;
                    $response = [
                        'status' => 200,
                        'token' => $token,
                        'user' => $userData,
                        'message' => 'Successfully Login! Welcome Back Employee',
                    ];
                    return response()->json($response);
                }  
        }
    }elseif($user->hasRole('super-admin')){
        $response = [
            'status' => 500,
            'message' => 'Kamu sedang login dengan akun admin tolong login sebagai pegawai',
        ];
        return response()->json($response);

    }else{
        $response = [
            'status' => 500,
            'message' => 'Please enter a valid data',
        ];
        return response()->json($response);
    }

}

    //LOGIN TOKEN VALIDATION

    public function userToken(Request $request){
        $user = $request->user();
    
        if(!$user){
            return response()->json(['error' => 'Token not valid'], 401);
        }
    
        return response()->json(['message' => 'Token valid', 'status' => 'VALID'],);
    }
    
    
    //---- PRESENCE FUNCTION ----\\ 

    //FUNCTION PRESENCE TODAY STATUS //BISA
    public function presenceToday($id) {
        $currentDate = Carbon::now('Asia/Jakarta')->toDateString();
    
        $attendance = Presence::with(['telework.statusCommit', 'worktrip.statusCommit', 'leave.statusCommit'])
                              ->where('user_id', $id)
                              ->whereDate('date', $currentDate)
                              ->orderByDesc('created_at')
                              ->first();
    
        if (!$attendance) {
            return response()->json(['status' => 'notAttended', 'carbon_date' => $currentDate]);
        }
    
        $teleworkStatus = $attendance->telework ? $attendance->telework->statusCommit->sortByDesc('created_at')->first()->status : null;
        $worktripStatus = $attendance->worktrip ? $attendance->worktrip->statusCommit->sortByDesc('created_at')->first()->status : null;
        $leaveStatus = $attendance->leave ? $attendance->leave->statusCommit->sortByDesc('created_at')->first()->status : null;
    
        // Handle Rejected Status
        if (($teleworkStatus && $teleworkStatus == 'rejected') || 
            ($worktripStatus && $worktripStatus == 'rejected') || 
            ($leaveStatus && $leaveStatus == 'rejected' && $attendance->date == $attendance->leave->start_date)) {
            return response()->json(['status' => 'canReAttend', 'message' => 'You can mark your attendance again', 'data' => $attendance ,'carbon_date' => $currentDate]);
        }
      
        
        // Handle Leave Category
        if ($attendance->category == 'leave') {
            $leave = Leave::where('user_id', $id)
            ->where('start_date', '<=', $currentDate)
            ->where('end_date', '>=', $currentDate)
            ->first();
    
            if (!$leave) {
                return response()->json(['status' => 'noRelatedLeave', 'message' => 'No related leave found.', 'carbon_date' => $currentDate]);
            }
    
            $leaveStatus = $leave->statusCommit->sortByDesc('created_at')->first()->status ?? null;
    
            if($leaveStatus == 'allowed' || $leaveStatus === null ){
                if($attendance->entry_time == '08:30:00' && $attendance->exit_time == '17:30:00'){
                    return response()->json(['status' => 'Leave', 'message' => 'You are currently on leave', 'carbon_date' => $currentDate]);
                }
            }
        }

                // Handle Pending Status
        if (($teleworkStatus && $teleworkStatus == 'pending') || ($worktripStatus && $worktripStatus == 'pending') || ($leaveStatus && $leaveStatus == 'pending')) {
            return response()->json(['status' => 'pendingStatus', 'message' => 'Your request is still pending. Wait for a moment for a response.', 'data' => $attendance,'carbon_date' => $currentDate]);
        } 

        if (($teleworkStatus && $teleworkStatus == 'preliminary') || ($worktripStatus && $worktripStatus == 'preliminary') || ($leaveStatus && $leaveStatus == 'preliminary')) {
            return response()->json(['status' => 'preliminaryStatus', 'message' => 'Your request is still preliminary. Wait for a moment for a response.', 'data' => $attendance,'carbon_date' => $currentDate]);
        } 
    
        // Handle other cases, like skipping
        if($attendance->category == 'skip' && $attendance->exit_time == '00:00:00' && $attendance->entry_time == '00:00:00' && $attendance->temporary_entry_time == '00:00:00') {
            return response()->json(['status' => 'Skipped', 'message' => 'You skipped work','carbon_date' => $currentDate, 'attendance_date' => $attendance->date]);
        } elseif ($attendance->exit_time == '00:00:00') {
            return response()->json(['status' => 'checkedIn', 'carbon_date' => $currentDate, 'attendance_date' => $attendance->date]);
        } else {
            return response()->json(['status' => 'checkedOut', 'carbon_date' => $currentDate, 'attendance_date' => $attendance->date]);
        }
    }
    

    //FUNCTION GET PRESENCE TODAY //BISAA
    public function getPresenceToday($id) {
        $currentDate = Carbon::now('Asia/Jakarta')->toDateString();
        
        $attendanceToday = Presence::with(['worktrip.statusCommit', 'telework.statusCommit', 'leave.statusCommit'])
                                  ->where('user_id', $id)
                                  ->whereDate('date', $currentDate)
                                  ->orderByDesc('created_at')
                                  ->first();
    
        if (!$attendanceToday) {
            return response()->json([
                'status' => 'notAttended',
                'category' => 'Belum check in',
                'entry_time' => '00:00 AM',
                'exit_time' => '00:00 AM',
                'date' => $currentDate,
                'system_date' => $currentDate,
                'carbon_date' => $currentDate,
                'user_id' => $id
            ]);
        }
    
        // Check for rejected status
        $teleworkStatus = $attendanceToday->telework ? $attendanceToday->telework->statusCommit->sortByDesc('created_at')->first()->status : null;
        $worktripStatus = $attendanceToday->worktrip ? $attendanceToday->worktrip->statusCommit->sortByDesc('created_at')->first()->status : null;
        $leaveStatus = $attendanceToday->leave ? $attendanceToday->leave->statusCommit->sortByDesc('created_at')->first()->status : null;
    
        if (($teleworkStatus && $teleworkStatus == 'rejected') || 
            ($worktripStatus && $worktripStatus == 'rejected') || 
            ($leaveStatus && $leaveStatus == 'rejected')) {
            return response()->json([
                'status' => 'Presence Again!',
                'category' => 'Presence Rejected',
                'presence' => $attendanceToday->category,
                'entry_time' => $attendanceToday->entry_time,
                'exit_time' => $attendanceToday->exit_time,
                'date' => $attendanceToday->date,
                'system_date' => $currentDate,
                'carbon_date' => $currentDate,
                'user_id' => $id
            ]);
        }
    
        if ($attendanceToday->category == 'skip') {
            return response()->json([
                'status' => 'Bolos',
                'category' => 'Bolos',
                'entry_time' => $attendanceToday->entry_time,
                'exit_time' => $attendanceToday->exit_time,
                'date' => $attendanceToday->date,
                'system_date' => $currentDate,
                'carbon_date' => $currentDate,
                'user_id' => $id
            ]);
        }
    
        // Default case is "attended".
        $category = 'Work From Office';  // Default
        if ($attendanceToday->category == 'telework') {
            $category = 'Work From Anywhere';
        } elseif ($attendanceToday->category == 'work_trip') {
            $category = 'Perjalanan Dinas';
        } elseif ($attendanceToday->category == 'leave') {
            $category = 'Cuti';
        }
    
        return response()->json([
            'status' => 'attended',
            'category' => $category,
            'entry_time' => $attendanceToday->entry_time,
            'exit_time' => $attendanceToday->exit_time,
            'date' => $attendanceToday->date,
            'system_date' => $currentDate,
            'carbon_date' => $currentDate,
            'user_id' => $id
        ]);
    }
    
    public function getPresenceTodayID($id) {
        $currentDate = Carbon::now('Asia/Jakarta')->toDateString();
    
        $attendanceToday = Presence::with(['user', 'worktrip', 'telework', 'leave'])
                                  ->where('user_id', $id)
                                  ->whereDate('date', $currentDate)
                                  ->first();
    
        if (!$attendanceToday) {
            return response()->json(['message' => 'Attendance not found for today'], 404);
        }
    
        // Map category for better presentation
        $categoriesMapping = [
            'WFO' => 'Work From Office',
            'telework' => 'Work From Anywhere',
            'work_trip' => 'Perjalanan Dinas',
            'leave' => 'Cuti',
            'skip' => 'Bolos'
        ];
        
        $category = $categoriesMapping[$attendanceToday->category] ?? $attendanceToday->category;
    
        $response = [
            'presence_id' => $attendanceToday->id,
            'user_id' => $attendanceToday->user_id,
            'name' => $attendanceToday->user->name, 
            'division' => $attendanceToday->user->employee->division->name, 
            'category' => $category,
            'date' => $attendanceToday->date
        ];
    
        return response()->json($response);
    }
    



    //FUNCTION GET PRESENCE //BISA 

    public function getPresence(Request $request) {
        $loggedInUserId = $request->id;
    $scope = $request->query('scope');
    $requestedPermissions = $request->has('permission') ? explode(',', $request->permission) : [];

    $user = User::with('employee', 'standups')->where('id', $loggedInUserId)->first();

    if (!$user || !$user->hasRole('employee')) {
        return response()->json([
            'status' => 500,
            'message' => 'Anda tidak memiliki akses sebagai employees.'
        ]);
    }

    $permissions = [
        'approve_preliminary', 
        'approve_allowed', 
        'reject_presence', 
        'view_request_pending', 
        'view_request_preliminary'
    ];
    $permissionNames = $user->getPermissionNames()->intersect($permissions)->values();

    $hasSpecialPermission = !$permissionNames->isEmpty();

    
        $statuses = [];
        if ($request->has('status') && $request->status != 'all') {
            $statuses = explode(',', $request->status);
        }
    
        $presenceQuery = Presence::with(['user', 'standup', 'worktrip', 'telework', 'leave']);

        $daysBeforeToday = $request->input('day', null);

        if ($daysBeforeToday !== null) {
            $endDate = Carbon::now(); // End date is today
            $startDate = $endDate->copy()->subDays($daysBeforeToday); // Subtract the specified number of days from the current date
            
            $presenceQuery->whereDate('date', '>=', $startDate);
            $presenceQuery->whereDate('date', '<=', $endDate);
        } else {
            if ($request->has('start_date')) {
                $startDate = Carbon::parse($request->start_date);
                $presenceQuery->whereDate('date', '>=', $startDate);
            }
            if ($request->has('end_date')) {
                $endDate = Carbon::parse($request->end_date);
                $presenceQuery->whereDate('date', '<=', $endDate);
            }
        }

        if ($request->has('date')) {
            $exactDate = Carbon::parse($request->date);
            $presenceQuery->whereDate('date', $exactDate);
        }

        if ($request->has('type')) {
            $type = $request->type;
            if ($type == 'WFA') {
                $presenceQuery->where('category', 'telework'); 
            } elseif ($type == 'PERJADIN') {
                $presenceQuery->where('category', 'work_trip'); 
            } elseif ($type == 'BOLOS') {
                $presenceQuery->where('category', 'skip'); 
            } elseif ($type == 'HISTORY'){
                $presenceQuery->whereIn('category', ['telework', 'work_trip', 'skip', 'WFO']); 
            } elseif($type == 'ALL'){
                $presenceQuery->whereIn('category', ['telework', 'work_trip', 'skip', 'WFO', 'leave']); 
            }
        }

        if ($scope === 'self' || !$hasSpecialPermission) {
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
                }elseif(in_array('rejected', $statuses)){
                    $query->orWhere('category', 'skip');
                }
            });
        }

        if (!empty($requestedPermissions)) {
            $presenceQuery->whereHas('user', function ($query) use ($requestedPermissions) {
                $query->whereHas('permissions', function ($innerQuery) use ($requestedPermissions) {
                    $innerQuery->whereIn('name', $requestedPermissions);
                });
            });
        }        

        function getLevelDescription($permission_level) {
            switch ($permission_level) {
                case 'approve_preliminary':
                    return 'Head of Tribe';
                case 'approve_allowed':
                    return 'Human Resource';
                default:
                    return 'Unknown'; 
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
                    'permission' => $presence->user->getPermissionNames()->intersect([
                        'approve_preliminary', 
                        'approve_allowed', 
                        'view_request_pending', 
                        'view_request_preliminary', 
                        'can_access_mobile'
                    ])->values() ?? null,
                    'category' => $presence->category,
                    'entry_time' => $presence->entry_time,
                    'exit_time' => $presence->exit_time,
                    'date' => $presence->date,
                    'latitude' => $presence->latitude,
                    'longitude' => $presence->longitude,
                    'emergency_description' => $presence->emergency_description,
                    'created_at' => $presence->created_at,
                    'updated_at' => $presence->updated_at,
                ];
    
                if ($presence->category === 'telework') {
                    $data['category_description'] = $presence->telework->category_description;
                    $data['telework_category'] = $presence->telework->telework_category;
                    $data['face_point'] = $presence->telework->face_point;
                    if ($presence->telework) {
                        $mostRecentStatus = $presence->telework->statusCommit->sortByDesc('created_at')->first();
                    
                        if ($mostRecentStatus && in_array($mostRecentStatus->status, ['allowed', 'rejected', 'preliminary'])) {
                            $approver = $mostRecentStatus->approver;
                        
                            if ($approver) { // Check if approver exists
                                $approverPermission = $approver->getPermissionNames()->intersect([
                                    'approve_preliminary', 
                                    'approve_allowed', 
                                    'reject_presence', 
                                    'view_request_pending', 
                                    'view_request_preliminary', 
                                    'can_access_mobile'
                                ])->values();
                        
                                if ($approverPermission && in_array($approverPermission, ['approve_preliminary','approve_allowed'])) {
                                    $data['approver_id'] = $approver->id;
                                    $data['approver_name'] = $approver->employee->first_name . ' ' . $approver->employee->last_name;
                                    $data['permission_approver'] = $approverPermission;
                                }
                            }
                        }
                        
                    }
                } elseif ($presence->category === 'work_trip') {
                    $worktripDetails = $presence->worktrip;
                
                    if ($worktripDetails) {
                        $data['file'] = $worktripDetails->file ?? 'null';
                        $filePath = $worktripDetails->file ?? 'null';
                        if ($filePath !== 'null') {
                            $data['originalFile'] = ($filePath !== 'null') ? basename($filePath) : 'null';
                        }                       
                        $data['face_point'] = $worktripDetails->face_point;
                
                        $mostRecentStatus = $worktripDetails->statusCommit->sortByDesc('created_at')->first();
                        
                        if ($mostRecentStatus && in_array($mostRecentStatus->status, ['allowed', 'rejected', 'preliminary'])) {
                            $approver = $mostRecentStatus->approver;
                            
                            if ($approver) { // Check if approver exists
                                $approverPermission = $approver->getPermissionNames()->intersect([
                                    'approve_preliminary', 
                                    'approve_allowed', 
                                    'reject_presence', 
                                    'view_request_pending', 
                                    'view_request_preliminary', 
                                    'can_access_mobile'
                                ])->values();
                            
                                if ($approverPermission && in_array($approverPermission, ['approve_preliminary','approve_allowed'])) {
                                    $data['approver_id'] = $approver->id;
                                    $data['approver_name'] = $approver->employee->first_name . ' ' . $approver->employee->last_name;
                                    $data['permission_approver'] = $approverPermission;
                                }
                            }
                        }
                    }
                }
                 elseif ($presence->category === 'leave') {
                    $relevantLeave = $presence->leave ?? Leave::with('leavedetail','substitute')->where('user_id', $presence->user_id)
                    ->where('start_date', '<=', $presence->date)
                    ->where('end_date', '>=', $presence->date)
                    ->first();

                    // dd($relevantLeave->leavedetail->typeofleave);

                    
                    if ($relevantLeave && $relevantLeave->leavedetail && $relevantLeave->leavedetail->typeofleave) {
                        $data['type'] = $relevantLeave->leavedetail->typeofleave->leave_name;
                        $data['description_leave'] = $relevantLeave->leavedetail->description_leave;
                        $data['submission_date'] = $relevantLeave->submission_date;
                        $data['total_leave_days'] = $relevantLeave->total_leave_days;
                        $data['start_date'] = $relevantLeave->start_date;
                        $data['end_date'] = $relevantLeave->end_date;
                        $data['entry_date'] = $relevantLeave->entry_date;
                        $data['substitute_id'] = $relevantLeave->substitute_id;
                        $data['substitute_name'] = $relevantLeave->substitute->name;
                        $data['substitute_division'] = $relevantLeave->substitute->employee->division->name;
                        $data['substitute_position'] = $relevantLeave->substitute->employee->position->name;
                        $data['file'] = $relevantLeave->file;
                        $filePath = $relevantLeave->file ?? 'null';
                        if ($filePath !== 'null') {
                            $data['originalFile'] = ($filePath !== 'null') ? basename($filePath) : 'null';
                        } 

                        if ($presence->leave) {
                            $mostRecentStatus = $presence->leave->statusCommit->sortByDesc('created_at')->first();
                            if ($mostRecentStatus && in_array($mostRecentStatus->status, ['allowed', 'rejected', 'preliminary'])) {
                                $approver = $mostRecentStatus->approver;
                            
                                if ($approver) { // Check if approver exists
                                    $approverPermission = $approver->getPermissionNames()->intersect([
                                        'approve_preliminary', 
                                        'approve_allowed', 
                                        'reject_presence', 
                                        'view_request_pending', 
                                        'view_request_preliminary', 
                                        'can_access_mobile'
                                    ])->values();
                            
                                    if ($approverPermission && in_array($approverPermission, ['approve_preliminary','approve_allowed'])) {
                                        $data['approver_id'] = $approver->id;
                                        $data['approver_name'] = $approver->employee->first_name . ' ' . $approver->employee->last_name;
                                        $data['permission_approver'] = $approverPermission;
                                    }
                                }
                            }
                            
                        }
                    }
                }
                
        
                $standups = $presence->standup->map(function ($standup) {
                    return [
                        'standup_id' => $standup->id,
                        'presence_id' => $standup->presence_id, // Assuming this exists
                        'done' => $standup->done,
                        'doing' => $standup->doing,
                        'blocker' => $standup->blocker,
                        'project' => $standup->project_id,
                        'project_name' => $standup->project->name,
                        'start_date' =>  $standup->project->start_date,
                        'end_date' =>  $standup->project->end_date,
                        'partner' => $standup->project->partnername, // Assuming this exists
                    ];
                })->toArray();
                
                if (count($standups) == 1) {
                    $data['standups'] = $standups[0];
                } else {
                    $data['standups'] = $standups;
                }
                
                if (isset($mostRecentStatus) && $mostRecentStatus) {
                    $data['status_commit_id'] = $mostRecentStatus->id;
                    $data['status'] = $mostRecentStatus->status;
                    $data['status_description'] = $mostRecentStatus->description;
                    $data['approver_name'] = $mostRecentStatus->approver_id
                    ? Employee::where('user_id', $mostRecentStatus->approver_id)
                        ->pluck(DB::raw("CONCAT(first_name, ' ', last_name) as full_name"))
                        ->first()
                    : null;
                    $data['approver_id'] = $mostRecentStatus->approver_id;
                }
    
                return $data;
            });
    
        if ($presence->isEmpty()) {
            return response()->json(['message' => 'Belum presence']);
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
            'emergency_description' => $presence->emergency_description,
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
            
                if ($mostRecentStatus && in_array($mostRecentStatus->status, ['allowed', 'rejected', 'preliminary'])) {
                    $approver = $mostRecentStatus->approver;
                
                    if ($approver) { // Check if approver exists
                        $approverPermission = $approver->getPermissionNames()->intersect([
                            'approve_preliminary', 
                            'approve_allowed', 
                            'reject_presence', 
                            'view_request_pending', 
                            'view_request_preliminary', 
                            'can_access_mobile'
                        ])->values();
                
                        if ($approverPermission && in_array($approverPermission, ['approve_preliminary','approve_allowed'])) {
                            $data['approver_id'] = $approver->id;
                            $data['approver_name'] = $approver->employee->first_name . ' ' . $approver->employee->last_name;
                            $data['permission_approver'] = $approverPermission;
                        }
                    }
                }
                
            }
        } elseif ($presence->category === 'work_trip' && $presence->worktrip) {
            $data['file'] = $presence->worktrip->file;
            $filePath = $presence->worktrip->file ?? 'null';
                        if ($filePath !== 'null') {
                            $data['originalFile'] = ($filePath !== 'null') ? basename($filePath) : 'null';
                        }   
            if ($presence->worktrip) {
                $mostRecentStatus = $presence->worktrip->statusCommit->sortByDesc('created_at')->first();
            
                if ($mostRecentStatus && in_array($mostRecentStatus->status, ['allowed', 'rejected', 'preliminary'])) {
                    $approver = $mostRecentStatus->approver;
                
                    if ($approver) { // Check if approver exists
                        $approverPermission = $approver->getPermissionNames()->intersect([
                            'approve_preliminary', 
                            'approve_allowed', 
                            'reject_presence', 
                            'view_request_pending', 
                            'view_request_preliminary', 
                            'can_access_mobile'
                        ])->values();
                
                        if ($approverPermission && in_array($approverPermission, ['approve_preliminary','approve_allowed'])) {
                            $data['approver_id'] = $approver->id;
                            $data['approver_name'] = $approver->employee->first_name . ' ' . $approver->employee->last_name;
                            $data['permission_approver'] = $approverPermission;
                        }
                    }
                }
                
            }
        } elseif ($presence->category === 'leave' && $presence->leave) {
            $relevantLeave = $presence->leave ?? Leave::with('leavedetail','substitute')->where('user_id', $presence->user_id)
                    ->where('start_date', '<=', $presence->date)
                    ->where('end_date', '>=', $presence->date)
                    ->first();

                    // dd($relevantLeave->leavedetail->typeofleave);
                    if ($relevantLeave && $relevantLeave->leavedetail && $relevantLeave->leavedetail->typeofleave) {
                        $data['type'] = $relevantLeave->leavedetail->typeofleave->leave_name;
                        $data['description_leave'] = $relevantLeave->leavedetail->description_leave;
                        $data['submission_date'] = $relevantLeave->submission_date;
                        $data['total_leave_days'] = $relevantLeave->total_leave_days;
                        $data['start_date'] = $relevantLeave->start_date;
                        $data['end_date'] = $relevantLeave->end_date;
                        $data['entry_date'] = $relevantLeave->entry_date;
                        $data['substitute_id'] = $relevantLeave->substitute_id;
                        $data['substitute_name'] = $relevantLeave->substitute->name;
                        $data['substitute_division'] = $relevantLeave->substitute->employee->division->name;
                        $data['substitute_position'] = $relevantLeave->substitute->employee->position->name;
                        $data['file'] = $relevantLeave->file;
                        $filePath = $relevantLeave->file ?? 'null';
                        if ($filePath !== 'null') {
                            $data['originalFile'] = ($filePath !== 'null') ? basename($filePath) : 'null';
                        } 

                        if ($presence->leave) {
                            $mostRecentStatus = $presence->leave->statusCommit->sortByDesc('created_at')->first();
                            if ($mostRecentStatus && in_array($mostRecentStatus->status, ['allowed', 'rejected', 'preliminary'])) {
                                $approver = $mostRecentStatus->approver;
                            
                                if ($approver) { // Check if approver exists
                                    $approverPermission = $approver->getPermissionNames()->intersect([
                                        'approve_preliminary', 
                                        'approve_allowed', 
                                        'reject_presence', 
                                        'view_request_pending', 
                                        'view_request_preliminary', 
                                        'can_access_mobile'
                                    ])->values();
                            
                                    if ($approverPermission && in_array($approverPermission, ['approve_preliminary','approve_allowed'])) {
                                        $data['approver_id'] = $approver->id;
                                        $data['approver_name'] = $approver->employee->first_name . ' ' . $approver->employee->last_name;
                                        $data['permission_approver'] = $approverPermission;
                                    }
                                }
                            }
                            
                        }
                    }
        }
    
        $standups = $presence->standup->map(function ($standup) {
            return [
                'standup_id' => $standup->id,
                'presence_id' => $standup->presence_id, // Assuming this exists
                'done' => $standup->done,
                'doing' => $standup->doing,
                'blocker' => $standup->blocker,
                'project' => $standup->project_id,
                'project_name' => $standup->project->name,
                'start_date' =>  $standup->project->start_date,
                'end_date' =>  $standup->project->end_date,
                'partner' => $standup->project->partnername, // Assuming this exists
            ];
        })->toArray();
        
        if (count($standups) == 1) {
            $data['standups'] = $standups[0];
        } else {
            $data['standups'] = $standups;
        }

        if (isset($mostRecentStatus) && $mostRecentStatus) {
            $data['status_commit_id'] = $mostRecentStatus->id;
            $data['status'] = $mostRecentStatus->status;
            $data['status_description'] = $mostRecentStatus->description;
            $data['approver_name'] = $mostRecentStatus->approver_id
            ? Employee::where('user_id', $mostRecentStatus->approver_id)
                ->pluck(DB::raw("CONCAT(first_name, ' ', last_name) as full_name"))
                ->first()
            : null;
            $data['approver_id'] = $mostRecentStatus->approver_id;
        }
    
        return response()->json(['message' => 'Success', 'data' => $data]);
    }


   

    //FUNCTION RESUME PRESENCE 

    
    public function getResumePresence(Request $request, $id) {
        $userPresences = Presence::where('user_id', $id)->get();
    
        $wfo = $userPresences->where('category', 'WFO');

        $telework = $userPresences->where('category', 'telework')->filter(function ($presence) {
            return $presence->telework->statusCommit->where('status', 'allowed')->count() > 0;
        });
    
        $work_trip = $userPresences->where('category', 'work_trip')->filter(function ($presence) {
            return $presence->worktrip->statusCommit->where('status', 'allowed')->count() > 0;
        });
    
        $skip = $userPresences->where('category', 'skip');

    
        $leave = $userPresences->where('category', 'leave')->filter(function ($presence) {
            return $presence->leave->statusCommit->where('status', 'allowed')->count() > 0;
        });
    
        return response()->json([
            'WFO' => $wfo->count(),
            'telework' => $telework->count(),
            'work_trip' => $work_trip->count(),
            'skip' => $skip->count(),
            'leave' => $leave->count(),
            'totalPresence' => $wfo->count() + $telework->count() + $work_trip->count()
        ]);
    }
    


    //FUNCTION STORE PRESENCE bisa


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

                    $facePointList = $request->input('face_point');

                    $facePointJson = json_encode($facePointList);

                    $facePointBase64 = base64_encode($facePointJson);
            
                    $file = $request->file('file');
            
                    $originalFilename = $file->getClientOriginalName();
                    $nama_lengkap = $user->employee ? $user->employee->first_name . '' . $user->employee->last_name : '';
                    
                    $directoryPath = 'files/presence/perjadin/' . $nama_lengkap;
                    $storedFilePath = $file->storeAs($directoryPath, $originalFilename, 'public');
            
                    if (!$storedFilePath) {
                        throw new \Exception("Error storing the file.");
                    }
            
                    $workTrip = WorkTrip::create([
                        'user_id' => $userId,
                        'presence_id' => $presence->id,
                        'file' => $storedFilePath, // This will now also be the original filename
                        'face_point' => $facePointBase64,
                    ]);
            
                    if (!$workTrip->statusCommit()->create(['status' => 'pending'])) {
                        throw new \Exception("Error creating status for WorkTrip.");
                    }

                    /// bikin notifikasi untuk ht.
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
                    ]);
    
                    if (!$telework->statusCommit()->create(['status' => 'pending'])) {
                        throw new \Exception("Error creating status for Telework.");
                    }

                    /// bikin notifikasi untuk ht.
                    
                    break;
            }
    
            $user->facePoint = $request->input('face_point');
            $user->save();

            $idUser = $request->input('user_id');
            $user = User::with('employee')->find($idUser);

            if ($user) {
                $userName = $user->name ?? '';
                $userDivision = $user->employee->division_id ?? '';
            } else {
                return response()->json(['message' => 'Failed: ' . $e->getMessage()], 500);
            }

            $category = $request->input('category');

            $inputDate = $request->input('date');

            $formattedDate = Carbon::parse($inputDate)->format('d F Y');

            switch ($category) {
                case 'telework':
                    $notificationMessage = $userName . ' Telah Mengajukan Work From Anywhere Pada Tanggal ' . $formattedDate;
                    break;
        
                case 'work_trip':
                    $notificationMessage = $userName . ' Telah Mengajukan Perjalanan Dinas Pada Tanggal ' . $formattedDate;
                    break;
        
                case 'WFO':
                    $notificationMessage = $userName . ' Telah Hadir Dengan Presensi Work From Office Pada Tanggal ' . $formattedDate;
                    break;
        
                default:
                    return response()->json(['message' => 'Presence category not recognized'], 400);
            }
     
            $notificationTitle = $userName ;
            
            $onesignalApiKey = 'MGEwNDI0NmMtOWIyMC00YzU5LWI3NDYtNzUxMjFjYjdmZGJj';
            $appId = 'd0249df4-3456-48a0-a492-9c5a7f6a875e';

            $notificationData = [
                'app_id' => $appId,
                'included_segments' => ['All'],
                'contents' => ['en' => $notificationMessage], 
                'headings' => ['en' => $notificationTitle], 
                'data' => [
                    'user_id' => $idUser,
                    'name' => $userName,
                    'divisi_id' => $userDivision,
                ],
            ];

            $responseN = Http::withHeaders([
                'Authorization' => 'Basic ' . $onesignalApiKey,
                'Content-Type' => 'application/json',
            ])->post('https://onesignal.com/api/v1/notifications', $notificationData);

            if ($responseN->successful()) {
                $notificationId = $responseN->json()['id'];
                $responseData = $responseN->json();
            } else {
                $error = $responseN->json();
            }
            
    
            DB::commit();  
    
            return response()->json([
                'message' => 'Success',
                'data' => $presence,
                'user' => $user,
                'notification_id' => $notificationId ?? null,
                'notif' => $responseData ?? null,
            ], 200);
    
        } catch (\Exception $e) {
            DB::rollBack();  
            \Log::error($e); 
            return response()->json(['message' => 'Failed: ' . $e->getMessage()], 500);
            
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
            $relatedWorktrip = Worktrip::with('user')->where('presence_id', $updateabsensi->id)->first();
            if (!$relatedWorktrip) {
                return response()->json(['message' => 'Related work_trip model not found'], 404);
            }
        
            $user = User::with('employee')->where('id', $relatedWorktrip->user_id)->first();

            $fullPath = $relatedWorktrip->file;
        
            if ($request->hasFile('file')) {
                // Delete the existing file if it exists
                if ($relatedWorktrip->file) {
                    Storage::disk('public')->delete($relatedWorktrip->file);
                }
            
                $file = $request->file('file');
                $originalFilename = $file->getClientOriginalName();
                $nama_lengkap = $user->employee 
                                ? $user->employee->first_name . ' ' . $user->employee->last_name 
                                : '';
                $directoryPath = 'files/presence/perjadin/' . $nama_lengkap;

                // Store the new file
                $file->storeAs($directoryPath, $originalFilename, 'public');

                // Update the fullPath with the new path
                $fullPath = $directoryPath . '/' . $originalFilename;
            }
        
            $relatedWorktrip->file = $fullPath;
            $relatedWorktrip->save();

            $relatedWorktrip->update($request->except('file'));
        }elseif ($updateabsensi->category == 'telework') {
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


        public function updateWorktripFromPresence(Request $request, $presenceId) {
          
            $presence = Presence::find($presenceId);
        
            if (!$presence) {
                return response()->json(['message' => 'Presence not found'], 404);
            }
        
            
            $relatedWorktrip = $presence->worktrip;
        
          
            if (!$relatedWorktrip) {
                $relatedWorktrip = WorkTrip::where('user_id', $presence->user_id)->first();
            }
        
            if (!$relatedWorktrip) {
                return response()->json(['message' => 'No Worktrip associated with this Presence'], 404);
            }

            $updateData = $request->only(['entry_time', 'longitude', 'latitude', 'face_point']);
            $presence->update($updateData);
        
            return response()->json(['message' => 'Presence updated successfully', 'data' => $presence]);
        }
    
    
    
    
    
    
    
    //FUNCTION DESTROY PRESENCE //BISA
    public function destroyPresence(Request $request, $id) {
        $presence = Presence::find($id);
    
        if (!$presence) {
            return response()->json(['message' => 'Presence record not found'], 404);
        }
    
        $startDate = null;
        $endDate = null;
    
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
    
            case 'leave':
                $leave = Leave::where('presence_id', $id)->first();
                if ($leave) {
                    $startDate = $leave->start_date;
                    $endDate = $leave->end_date;
                    $leave->statusCommit()->delete();
                    $leave->delete();
                }
                break;
    
            case 'WFO':
                // No specific action needed for WFO category
                break;
    
            default:
                return response()->json(['message' => 'Presence category not recognized'], 400);
        }
    
        // Delete all presence records that fall within the start and end dates of the leave/worktrip
        if ($startDate && $endDate) {
            Presence::where('user_id', $presence->user_id)
                    ->where('category', $presence->category)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->delete();
        } else {
            // If there's no start and end date, just delete the specific presence record
            $presence->delete();
        }
    
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

//EMERGENCY //BISA

    public function emergencyCheckOut(Request $request)
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
        'exit_time' => Carbon::now()->toTimeString(),
        'emergency_description' => $request->input('emergency_description')
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
        $attendanceToday     = null;
        $errors = [];
        
        if (!$request->has('status') || !in_array($request->input('status'), ['rejected', 'allowed', 'preliminary'])) {
            $errors['status'] = 'The status field is required and must be one of: rejected, allowed, preliminary.';
        }
        
        if ($request->has('description') && !is_string($request->input('description'))) {
            $errors['description'] = 'The description must be a string.';
        }
        
        if (in_array($request->input('status'), ['rejected', 'allowed']) && !$request->has('description')) {
            $errors['description'] = 'The description is required when status is rejected or allowed.';
        }
        
        $approver = User::find($request->input('approver_id'));
        
        if (!$request->has('approver_id') || !$approver) {
            $errors['approver_id'] = 'The approver id field is required and must exist in the users table.';
        } else {
            // Check permissions based on the status
            switch ($request->input('status')) {
                case 'preliminary':
                    if (!$approver->hasPermissionTo('approve_preliminary')) {
                        $errors['approver_id'] = 'The user must have the approve_preliminary permission for preliminary status.';
                    }
                    break;
        
                case 'allowed':
                    if (!$approver->hasPermissionTo('approve_allowed')) {
                        $errors['approver_id'] = 'The user must have the approve_allowed permission for allowed status.';
                    }
                    break;
        
                case 'rejected':
                    if (!$approver->hasPermissionTo('reject_presence')) {
                        $errors['approver_id'] = 'The user must have the reject_presence permission to reject.';
                    }
                    break;
        
                default:
                    // Handle unknown statuses, though it's already checked at the start.
                    $errors['status'] = 'Invalid status.';
                    break;
            }
        }
        
        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 400);
        }
        

        DB::table('status_commits')->where('id', $id)->update([
            'approver_id' => $request->input('approver_id'),
            'status' => $request->input('status'),
            'description' => $request->input('description')
        ]);

        $statusCommit = StatusCommit::with('statusable')->findOrFail($id);
        $statusable = $statusCommit->statusable;



        if ($statusable->presence) {
            $statusable->update($request->only(['status', 'description', 'approver_id','entry_time']));
            
            if ($statusable->presence->category == 'work_trip' && $request->input('status') === 'allowed') {

                $submissionDate = Carbon::parse($statusable->presence->date);
                $presenceForSubmissionDate = Presence::firstOrNew([
                    'user_id' => $statusable->user_id,
                    'date' => $submissionDate->toDateString()
                ]);
            
                $presenceForSubmissionDate->entry_time = '08:30:00';
                $presenceForSubmissionDate->exit_time = '00:00:00';
                $presenceForSubmissionDate->category = 'work_trip';
                $presenceForSubmissionDate->save();
            
                $statusable->presence_id = $presenceForSubmissionDate->id;
                $statusable->save();
            
            }            
            elseif ($statusable->presence->category == 'leave' && $request->input('status') === 'allowed') {
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
            } elseif ($statusable->presence->category == 'telework' && $request->input('status') === 'allowed') {
                $submissionDate = Carbon::parse($statusable->presence->date); 
                $attendanceToday = Presence::where('user_id', $statusable->user_id)
                                        ->whereDate('date', $submissionDate->toDateString())
                                        ->first();

                // dd($attendanceToday);
            
                if($attendanceToday) {
                    $attendanceToday->update([
                        'entry_time' => '08:30:00',
                        'exit_time' => '00:00:00',
                        'category' => 'telework'
                    ]);
                }
            }
            
        }
        return response()->json(['message' => 'Approval status saved successfully.', 'data' => $statusCommit->fresh(), 'absensi' => $statusable->fresh(), 'presence' => $attendanceToday ? $attendanceToday->fresh() : null], 200);
    }

    //---- STAND UP FUNCTION ----\\

    //FUNCTION GET STAND UP //BISA

    public function getStandUp(Request $request) {
        $endOfToday = Carbon::today()->endOfDay();
        $today = Carbon::today();
        $currentYear = date('Y');


        $startOfLastMonth = Carbon::today()->subMonth()->startOfDay();

        $scope = $request->query('scope');
    
        // Query to fetch StandUp data for a specific user
        if ($request->has('id')) {
            $user = User::with('employee', 'standups')->where('id', $request->id)->first();
    
            if (!$user || !$user->hasRole('employee')) {
                return response()->json([
                    'status' => 500,
                    'message' => 'Anda tidak memiliki akses sebagai employees.',
                ]);
            }
    
            $query = StandUp::with('user', 'project', 'presence')->where('user_id', $request->id);
            
            if ($scope == 'month'){
                $query->whereBetween('created_at', [$startOfLastMonth, $endOfToday]);
            }elseif($scope == 'year'){
                $query->whereYear('created_at', $currentYear);
            }else{
                return response()->json([
                    'status' => 500,
                    'message' => 'Tolong lebih spesifik lagi.',
                ]);
            }
        } else {
            $query = StandUp::with('user', 'project', 'presence')
                            ->whereDate('created_at', $today);
        }
    
        $standUps = $query->orderBy('updated_at', 'desc')->get();
    
        // Process the standups
        $processedStandUps = $standUps->map(function ($standUp) {
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
                'presence_id' => $standUp->presence_id,
                'presence_category' => $standUp->presence->category,
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
    
        if ($processedStandUps->isEmpty()) {
            return response()->json(['message' => 'Belum ada yang stand up']);
        } else {
            return response()->json(['message' => 'Success', 'data' => $processedStandUps]);
        }
    }
    
    

    public function getProject(Request $request){

        if ($request->has('id')) {
            $query = Project::with('partner')
            ->where('id', $request->id);
        } else {
            $query = Project::with('partner');
        }
    
        $projects = $query->orderBy('updated_at', 'desc')->get();
    
        
        $project = $projects
        ->map(function ($project) {
            $currentDate = Carbon::now();
            $start_date = Carbon::parse($project->start_date);
            $end_date = Carbon::parse($project->end_date);
            $totalDays = $start_date->diffInDays($end_date) + 1;

            $status = '';

            if($currentDate > $end_date){
                $status = 'Tidak Aktif';
            }else{
                $status = 'Aktif';
            }
            
            

            return [
                'id' => $project->id,
                'partner_id' => $project->partner_id,
                'project' => $project->name,
                'partner' => $project->partner->name,
                'partner_logo' => $project->partner->logo,
                'partner_description' => $project->partner->description,
                'start_date' => $project->start_date,
                'end_date' => $project->end_date,
                'durasi' => $totalDays,
                'status' => $status,
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
    
        $leaveQuery = Leave::with(['user', 'presence', 'statusCommit','leavedetail','substitute'])->orderBy('leaves.updated_at', 'desc')
        ;
    
        if ($userId) {
            $leaveQuery = $leaveQuery->where('user_id', $userId);
        }

        if ($jenisleave) {
            $leaveQuery = $leaveQuery->whereHas('leavedetail', function ($query) use ($jenisleave) {
                $query->where('type_of_leave_id', $jenisleave);
            });
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
                'substitute_id' => $leave->substitute_id,
                'substitute_name' => $leave->substitute->name ?? 'unknown',
                'substitute_division' => $leave->substitute->employee->division->name ?? 'unknown',
                'substitute_position' => $leave->substitute->employee->position->name ?? 'unknown',
                'nama_lengkap' => $nama_lengkap,
                'type' => $leave->leavedetail->typeofleave->leave_name,
                'leave_detail_id' => $leave->leave_detail_id,
                'description_leave' => $leave->leavedetail->description_leave,
                'entry_time' => ($mostRecentStatus && $mostRecentStatus->status == 'allowed') ? $leave->presence->entry_time : null,
                'exit_time' => ($mostRecentStatus && $mostRecentStatus->status == 'allowed') ? $leave->presence->exit_time : null,
                'category' => ($mostRecentStatus && $mostRecentStatus->status == 'allowed') ? $leave->presence->category : null,
                'posisi' => $leave->user->employee->position->name,
                'submission_date' => $leave->submission_date,
                'start_date' => $leave->start_date,
                'end_date' => $leave->end_date,
                'entry_date' => $leave->entry_date,
                'total_leave_days' => $leave->total_leave_days,
                'file' => $leave->file,
                'originalFile' => basename($leave->file),
                'status' => $mostRecentStatus ? $mostRecentStatus->status : null,
                'status_description' => $mostRecentStatus ? $mostRecentStatus->description : null, 
                'approver_id' => $mostRecentStatus ? $mostRecentStatus->approver_id : null, 
                'approver_name' => $approver_name, 
                'status_commit_id' => $mostRecentStatus ? $mostRecentStatus->id : null,
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

    public function getLeaveById(Request $request, $id) {
        $leave = Leave::with(['user', 'user.employee', 'statusCommit', 'leavedetail', 'substitute'])->find($id);
    
        if (!$leave) {
            return response()->json(['status' => 404, 'message' => 'Leave not found']);
        }
    
        $nama_lengkap = ($leave->user && $leave->user->employee) 
                       ? $leave->user->employee->first_name .' '. $leave->user->employee->last_name 
                       : null;
    
        $mostRecentStatus = $leave->statusCommit->sortByDesc('created_at')->first();
        $approver_name = $mostRecentStatus && $mostRecentStatus->approver 
                       ? $mostRecentStatus->approver->employee->first_name .' '. $mostRecentStatus->approver->employee->last_name 
                       : null;
    
        $data = [
            'id' => $leave->id,
            'user_id' => $leave->user_id,
            'substitute_id' => $leave->substitute_id,
            'substitute_name' => $leave->substitute->name ?? 'unknown',
            'substitute_division' => $leave->substitute->employee->division->name ?? 'unknown',
            'substitute_position' => $leave->substitute->employee->position->name ?? 'unknown',
            'nama_lengkap' => $nama_lengkap,
            'type' => $leave->leavedetail->typeofleave->leave_name,
            'leave_detail_id' => $leave->leave_detail_id,
            'description_leave' => $leave->leavedetail->description_leave,
            'entry_time' => ($mostRecentStatus && $mostRecentStatus->status == 'allowed') ? $leave->presence->entry_time : null,
            'category' => ($mostRecentStatus && $mostRecentStatus->status == 'allowed') ? $leave->presence->category : null,
            'posisi' => $leave->user->employee->position->name,
            'submission_date' => $leave->submission_date,
            'start_date' => $leave->start_date,
            'end_date' => $leave->end_date,
            'entry_date' => $leave->entry_date,
            'total_leave_days' => $leave->total_leave_days,
            'file' => $leave->file,
            'originalFile' => basename($leave->file),
            'status' => $mostRecentStatus ? $mostRecentStatus->status : null,
            'status_description' => $mostRecentStatus ? $mostRecentStatus->description : null,
            'approver_id' => $mostRecentStatus ? $mostRecentStatus->approver_id : null,
            'approver_name' => $approver_name,
            'created_at' => $leave->created_at,
            'updated_at' => $leave->updated_at
        ];
    
        return response()->json(['message' => 'Success', 'data' => $data]);
    }
    

    public function getLeaveCount(Request $request) {
        $userId = $request->query('id');
        $jenisleave = $request->query('type');
        
        $currentYear = now()->year;
        
        $leaveQuery = Leave::with(['user', 'presence', 'statusCommit','leavedetail'])->whereHas('statusCommit', function($query) {
            $query->where('status', 'allowed');
        })->orderBy('leaves.updated_at', 'desc')
        ;
        
        if ($userId) {
            $leaveQuery = $leaveQuery->where('user_id', $userId);
        }
        
        if ($jenisleave) {
            $leaveQuery->join('leave_detail', 'leaves.leave_detail_id', '=', 'leave_detail.id')
                ->where('leave_detail.type_of_leave_id', $jenisleave);
        }
    
        
        $leaves = $leaveQuery->get();
        
        $leaveCounts = [
            'exclusive' => 0,
            'yearly' => 0,
            'emergency' => 0
        ];
    
        foreach ($leaves as $leave) {
            $type = $leave->leavedetail->typeofleave->leave_name;
            $days = $leave->total_leave_days;
            
 
            $mostRecentStatus = $leave->statusCommit->sortByDesc('created_at')->first();
            if (!$mostRecentStatus || $mostRecentStatus->status !== 'allowed') {
                continue;
            }
    
            if ($type === 'yearly' && Carbon::parse($leave->start_date)->year !== $currentYear) {
                continue; 
            }
    
            if (array_key_exists($type, $leaveCounts)) {
                $leaveCounts[$type] += $days;
            }
        }
        
        return response()->json(['message' => 'Success', 'data' => $leaveCounts]);
    }
        
    public function yearlyLeave(Request $request) {
        $userId = $request->query('id');
        
        $currentYear = now()->year; 

        $leaveQuery = Leave::with(['user', 'presence', 'statusCommit','leavedetail'])
                            ->whereHas('statusCommit', function($query) {
                                $query->where('status', 'allowed');
                            })
                            ->orderBy('updated_at', 'desc');
        
        if ($userId) {
            $leaveQuery = $leaveQuery->where('user_id', $userId);
        }
        
        $leaveQuery = $leaveQuery->whereHas('leavedetail.typeofleave', function($query) {
            $query->where('leave_name', 'yearly');
        });
        
        $leaves = $leaveQuery->get();
        
        $yearlyLeaveDays = 0;
    
        foreach ($leaves as $leave) {
            if (Carbon::parse($leave->start_date)->year === $currentYear) {
                $yearlyLeaveDays += $leave->total_leave_days;
            }
        }
        
        return response()->json(['message' => 'Success', 'data' => $yearlyLeaveDays]);
    }


        public function getLeaveDetailOption(Request $request) {
        
            $type = $request->query('type');
            $leaveQuery = LeaveDetail::with(['typeofleave'])->orderBy('updated_at', 'desc');

            if($type){
                $leaveQuery->where('type_of_leave_id', $type );
            }
        
            $leavedesc = $leaveQuery->get()->map(function ($leavedesc) {
            
            
                return [
                    'id' => $leavedesc->id,
                    'description_leave' => $leavedesc->description_leave,
                    'type_of_leave_id' => $leavedesc->type_of_leave_id,
                    'days' => $leavedesc->days,
                    'type_leave' => $leavedesc->typeofleave->leave_name,
                    'created_at' => $leavedesc->created_at,
                    'updated_at' => $leavedesc->updated_at,
                ];
            });
            
        
            if ($leavedesc->isEmpty()) {
                return response()->json(['message' => 'Data leave description kosong']);
            } else {
                return response()->json(['message' => 'Success', 'data' => $leavedesc]);
            }
        }
    
    // FUNCTION STORE LEAVE //BISA

    public function storeLeave(Request $request) {
        $currentDate = Carbon::now();
        $submissionDate = Carbon::parse($request->input('submission_date'));
        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));
        $totalDays = $startDate->diffInDays($endDate) + 1;
        $userId = $request->input('user_id');
        $user = User::with('employee', 'standups')->where('id', $userId)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        if ($request->has('file')) {
            $file = $request->file('file');
    
            $originalFilename = $file->getClientOriginalName();
            $nama_lengkap = $user->employee ? $user->employee->first_name . '' . $user->employee->last_name : '';
    
            $directoryPath = 'files/presence/cuti/' . $nama_lengkap;
            $storedFilePath = $file->storeAs($directoryPath, $originalFilename, 'public');
    
            if (!$storedFilePath) {
                throw new \Exception("Error storing the file.");
            }
        }
    
        DB::beginTransaction();
    
        try {
    
            $presence = Presence::create([
                'user_id' => $request->input('user_id'),
                'category' => 'leave',
                'entry_time' => '00:00:00',
                'exit_time' => '00:00:00',
                'date' => $startDate->toDateString(),
            ]);
    
            $leave = Leave::create([
                'user_id' => $request->input('user_id'),
                'leave_detail_id' => $request->input('leave_detail_id'),
                'substitute_id' => $request->input('substitute_id'),
                'file' => $storedFilePath ?? null,
                'submission_date' => $submissionDate,
                'total_leave_days' => $totalDays,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'presence_id' => $presence->id,
                'entry_date' => $request->input('entry_date'),
            ]);
    
            if (!$leave->statusCommit()->exists()) {
                $leave->statusCommit()->create([
                    'approver_id' => null,
                    'status' => 'pending',
                    'description' => null,
                ]);
            }
    
            // Mengambil informasi user untuk notifikasi
            $userName = $user->name ?? '';
            $userDivision = $user->employee->division_id ?? '';
    
            // Membuat pesan notifikasi berdasarkan kategori
            $notificationMessage = $userName . ' Telah Mengajukan Cuti Pada Tanggal ' . $startDate->format('d F Y');
    
            // Mengambil API key dan app ID OneSignal
            $onesignalApiKey = 'MGEwNDI0NmMtOWIyMC00YzU5LWI3NDYtNzUxMjFjYjdmZGJj';
            $appId = 'd0249df4-3456-48a0-a492-9c5a7f6a875e';
    
            // Menyiapkan data notifikasi
            $notificationData = [
                'app_id' => $appId,
                'included_segments' => ['All'],
                'contents' => ['en' => $notificationMessage],
                'headings' => ['en' => $userName],
                'data' => [
                    'user_id' => $userId,
                    'name' => $userName,
                    'divisi_id' => $userDivision,
                ],
                'vibrate' => [500, 250, 500], 
            ];
    
            // Mengirim notifikasi ke OneSignal
            $responseN = Http::withHeaders([
                'Authorization' => 'Basic ' . $onesignalApiKey,
                'Content-Type' => 'application/json',
            ])->post('https://onesignal.com/api/v1/notifications', $notificationData);
    
            if ($responseN->successful()) {
                $notificationId = $responseN->json()['id'];
                $responseData = $responseN->json();
            } else {
                $error = $responseN->json();
            }
    
            DB::commit();
    
            return response()->json([
                'message' => 'Success',
                'data' => $leave,
                'notification_id' => $notificationId ?? null,
                'notif' => $responseData ?? null,
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e);
            return response()->json(['message' => 'Failed to create leave: ' . $e->getMessage()], 500);
        }
    }
    

    public function updateLeave(Request $request, $id) {

        $leave = Leave::find($id);

        if (!$leave) {
            $presence = Presence::find($id);
            if($presence) {
                $leave = $presence->leave; 
            }
        }
    
        if (!$leave) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $updatableFields = ['user_id', 'leave_detail_id', 'submission_date', 'start_date', 'end_date', 'entry_date','substitute_id'];

        $dataToUpdate = $request->only($updatableFields);

        if(isset($dataToUpdate['start_date']) && isset($dataToUpdate['end_date'])) {
            $startDate = Carbon::parse($dataToUpdate['start_date']);
            $endDate = Carbon::parse($dataToUpdate['end_date']);
            $dataToUpdate['total_leave_days'] = $startDate->diffInDays($endDate) + 1;
        }

        if($request->has('file')) {
            $file = $request->file('file');
    
            if($leave->file && Storage::disk('public')->exists($leave->file)) {
                Storage::disk('public')->delete($leave->file);
            }
    
            $originalFilename = $file->getClientOriginalName();
            $nama_lengkap = $leave->user->employee 
                            ? $leave->user->employee->first_name . ' ' . $leave->user->employee->last_name 
                            : '';
            
            $directoryPath = 'files/presence/cuti/' . $nama_lengkap;
            $storedFilePath = $file->storeAs($directoryPath, $originalFilename, 'public');
    
            if (!$storedFilePath) {
                throw new \Exception("Error storing the file.");
            }
    
            $dataToUpdate['file'] = $storedFilePath;
        }
    
        DB::beginTransaction();  
    
        try {
            $leave->update($dataToUpdate);
            DB::commit();  
            return response()->json(['message' => 'Update successful', 'data' => $leave]);
        } catch (\Exception $e) {
            DB::rollBack();  
            return response()->json(['message' => 'Failed to update leave: ' . $e->getMessage()], 500);
        }
    }  

    //FUNCTION DELETE LEAVE //BISA
    public function destroyLeave($id) {
        $leave = Leave::with('statusCommit')->find($id);
    
        if (!$leave) {
            return response()->json(['message' => 'Record not found'], 404);
        }
    
        Presence::where('user_id', $leave->user_id)
                ->where('category', 'leave')
                ->whereBetween('date', [$leave->start_date, $leave->end_date])
                ->delete();
    
        $leave->statusCommit()->delete();
        $leave->delete();
    
        return response()->json(['message' => 'Delete successful']);
    }

    //---- PROFILE FUNCTION ----\\

    public function getProfile(Request $request){

            $employee = Employee::with('user','division','position')->where('user_id', $request->user_id)->orderBy('updated_at', 'desc')->get()
            ->map(function ($employee) {
                $nama_lengkap = $employee->first_name .' '. $employee->last_name;
    
                return [
                    'id' => $employee->id,
                    'user_id' => $employee->user_id,
                    'nama_lengkap'=> $nama_lengkap,
                    'divisi' => $employee->division->name,
                    'division_id' => $employee->division_id,
                    'posisi' => $employee->position->name,
                    'posisition_id' => $employee->position_id,
                    'avatar' => $employee->avatar,
                    'id_number' => $employee->id_number,
                    'gender' => $employee->gender,
                    'address' => $employee->address,
                    'birth_date' => $employee->birth_date,
                    'is_active' => $employee->is_active,
                    'permission' => $employee->user->getPermissionNames()->intersect([
                        'approve_preliminary', 
                        'approve_allowed', 
                        'reject_presence', 
                        'view_request_pending', 
                        'view_request_preliminary', 
                        'can_access_mobile'
                    ])->values(),
                    'name' => $employee->user->name,
                    'email' => $employee->user->email,
                    'email_verified_at' => $employee->user->email_verified_at,
                    'role' => $employee->user->getRoleNames()->first(),
                    'password' => $employee->user->password,
                    'facepoint' => $employee->user->facePoint,
                    'remember_token' => $employee->user->remember_token,
                    'standup' => $employee->user->standups,
                    'done_count' => $employee->user->standups->where('done', true)->count(),
                    'doing_count' => $employee->user->standups->where('doing', true)->count(),
                    'blocker_count' => $employee->user->standups->where('blocker', true)->count(),
                    'created_at' => $employee->created_at,
                    'updated_at' => $employee->updated_at,
                ];
            });
    
            if ($employee->isEmpty()) {
                return response()->json(['message' => 'Bukan employee']);
            } else {
                return response()->json(['message' => 'Success', 'data' => $employee]);
            }
    }

    public function getUser(Request $request){
    $division = $request->query('division');
    $position = $request->query('position');
    $permission = $request->query('permission');

    $employeeQuery = Employee::with('user','division','position')->orderBy('updated_at', 'desc');
            
    if($division){
        $employeeQuery->where('division_id', $division );
    }

    if($position){
        $employeeQuery->where('position_id', $position );
    }

    $permissionFilters = [];
    if($permission){
        switch ($permission) {
            case 'ht':
                $permissionFilters = [
                    'approve_preliminary',
                    'reject_presence',
                    'view_request_pending'
                ];
                break;

            case 'hr':
                $permissionFilters = [
                    'approve_allowed',
                    'reject_presence',
                    'view_request_preliminary'
                ];
                break;
            
            default:
                //
                break;
        }
    }

    $employees = $employeeQuery->get()
        ->filter(function($employee) use ($permissionFilters) {
            // Check if user has all required permissions
            foreach ($permissionFilters as $perm) {
                if (!$employee->user->hasPermissionTo($perm)) {
                    return false;
                }
            }
            return true;
        })
        ->map(function ($employee) {
            $nama_lengkap = $employee->first_name .' '. $employee->last_name;
    
                return [
                    'id' => $employee->id,
                    'user_id' => $employee->user_id,
                    'nama_lengkap'=> $nama_lengkap,
                    'divisi' => $employee->division->name,
                    'divisi_id' => $employee->division->id,
                    'posisi' => $employee->position->name,
                    'posisi_id' => $employee->position->id,
                    'avatar' => $employee->avatar,
                    'id_number' => $employee->id_number,
                    'gender' => $employee->gender,
                    'address' => $employee->address,
                    'birth_date' => $employee->birth_date,
                    'is_active' => $employee->is_active,
                    'permission' => $employee->user->getPermissionNames()->intersect([
                        'approve_preliminary', 
                        'approve_allowed', 
                        'reject_presence', 
                        'view_request_pending', 
                        'view_request_preliminary', 
                        'can_access_mobile'
                    ])->values(),
                    'name' => $employee->user->name,
                    'created_at' => $employee->created_at,
                    'updated_at' => $employee->updated_at,
                ];
        });

        if ($employees->isEmpty()) {
            return response()->json(['message' => 'Tidak ada employee berdasarkan divisi atau posisi tersebut']);
        } else {
            return response()->json(['message' => 'Success', 'data' => $employees]);
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