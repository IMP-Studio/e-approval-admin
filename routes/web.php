<?php

use App\Models\Partner;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ApproveController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\superAdminController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
})->middleware('autologout');
Route::get('/apaboy', [HomeController::class, 'boy'])->name('boy');

Route::get('/back', [HomeController::class, 'back'])->name('back');

Route::get('/privacy-policy', function(){
    return view('privacypolicy');
});

Auth::routes();
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/standup', [HomeController::class, 'standup'])->middleware('permission:view_standups')->name('standup');
    Route::get('/standup/export/{year}/{month}', [HomeController::class, 'exportStandup'])->name('standup.excel');

    Route::get('/presence', [PresenceController::class, 'index'])->middleware('permission:view_presences')->name('presence');
    Route::get('/attendance/export/{year}', [PresenceController::class, 'exportExcel'])->name('presence.excel');

    Route::post('/attendance/exportByRange', [PresenceController::class, 'exportExcelByRange'])->name('presence.excelByRange');

    Route::prefix('presence')->group(function () {
        Route::delete('/destroy/{id}', [PresenceController::class, 'destroy'])->name('presence.destroy');
    });

    Route::prefix('standup')->group(function(){
        Route::delete('/destroy/{id}', [HomeController::class, 'destroy'])->name('standup.destroy');
    });

    Route::prefix('division')->middleware('permission:view_divisions')->group(function () {
        Route::get('/', [DivisionController::class, 'index'])->name('divisi');
        Route::get('/create', [DivisionController::class, 'create'])->name('divisi.create');
        Route::post('/store', [DivisionController::class, 'store'])->name('divisi.store');
        Route::get('/edit/{id}', [DivisionController::class, 'edit'])->name('divisi.edit');
        Route::put('/update/{id}', [DivisionController::class, 'update'])->name('divisi.update');
        Route::delete('/destroy/{id}', [DivisionController::class, 'destroy'])->name('divisi.destroy');
        Route::get('/export_excel', [DivisionController::class,'export_excel'])->name('division.excel');
        Route::get('/template_excel', [DivisionController::class,'downloadTemplate'])->name('division.downloadTemplate');
        Route::post('/import_excel', [DivisionController::class,'importExcel'])->name('division.import');
        Route::get('/detail/{id}', [DivisionController::class,'detailDivisi'])->name('division.detail');
    });

    Route::prefix('position')->middleware('permission:view_positions')->group(function () {
        Route::get('/', [PositionController::class, 'index'])->name('position');
        Route::post('/store', [PositionController::class, 'store'])->name('position.store');
        Route::put('/update/{id}', [PositionController::class, 'update'])->name('position.update');
        Route::delete('/destroy/{id}', [PositionController::class, 'destroy'])->name('position.destroy');
        Route::get('/detail/{id}', [PositionController::class,'detailPosition'])->name('position.detail');
        Route::get('/template_excel', [PositionController::class,'downloadTemplate'])->name('position.downloadTemplate');
        Route::post('/import_excel', [PositionController::class,'importExcel'])->name('position.import');
        Route::get('/export_excel', [PositionController::class,'exportExcelStandup'])->name('position.excel');
    });

    Route::prefix('employee')->middleware('permission:view_employees')->group(function () {
        Route::get('/', [EmployeeController::class,'index'])->name('employee');
        Route::get('/create', [EmployeeController::class,'create'])->name('employee.create');
        Route::post('/store', [EmployeeController::class,'store'])->name('employee.store');
        Route::get('/edit/{id}', [EmployeeController::class,'edit'])->name('employee.edit');
        Route::put('/update/{id}', [EmployeeController::class,'update'])->name('employee.update');
        Route::get('/getPositionEdit', [EmployeeController::class,'getDataPosition'])->name('employee.editGetposition');
        Route::delete('/destroy/{id}', [EmployeeController::class,'destroy'])->name('employee.destroy');
        Route::get('/trash', [EmployeeController::class,'trash'])->name('employee.trash');
        Route::get('/export_excel', [EmployeeController::class,'export_excel'])->name('employee.excel');
        Route::get('/export_excel_notrecap', [EmployeeController::class,'export_excel_notrecap'])->name('employee.excelnotrecap');
        Route::get('/export_pdf', [EmployeeController::class,'export_pdf'])->name('employee.pdf');
        Route::get('/template_excel', [EmployeeController::class,'downloadTemplate'])->name('employee.downloadTemplate');
        Route::post('/import_excel', [EmployeeController::class,'importExcel'])->name('employee.import');
        Route::get('/get-positions/{id}', [EmployeeController::class, 'getPositions']);
        Route::put('/restore/{id}', [EmployeeController::class, 'restore'])->name('employee.restore');
        Route::delete('/destory/permanently/{id}', [EmployeeController::class, 'destroyPermanently'])->name('employee.destroy.permanently');
    });

        Route::prefix('partner')->middleware('permission:view_partners')->group(function () {
            Route::get('/', [PartnerController::class,'index'])->name('partner');
            Route::post('/store', [PartnerController::class,'store'])->name('partner.store');
            Route::put('/update/{id}', [PartnerController::class,'update'])->name('partner.update');
            Route::delete('/destroy/{id}', [PartnerController::class,'destroy'])->name('partner.destroy');
            Route::get('/detail/{id}', [PartnerController::class,'detailpartner'])->name('partner.detail');
            Route::get('/export_excel', [PartnerController::class, 'export_excel'])->name('partner.excel');
            Route::get('/template_excel', [PartnerController::class,'downloadTemplate'])->name('partner.downloadTemplate');
            Route::post('/import_excel', [PartnerController::class,'importExcel'])->name('partner.import');
        }); 

        Route::prefix('permission')->group(function () {
            Route::get('/', [PermissionController::class, 'index'])->name('permission');
            // Route::get('/set-permission/{modelType}/{modelId}', [PermissionController::class, 'setModelPermission'])->name('permission.set_permission');
            Route::get('/user/{userId}/permissions', [PermissionController::class, 'getUserPermissions'])->name('permission.user_permissions');
            Route::post('/user/{userId}/permissions', [PermissionController::class, 'setModelPermissions'])->name('permission.user.set_permissions');
        });

    Route::prefix('project')->middleware('permission:view_projects')->group(function () {
           Route::get('/', [ProjectController::class,'index'])->name('project');
           Route::post('/store', [ProjectController::class,'store'])->name('project.store');
           Route::put('/update/{id}', [ProjectController::class,'update'])->name('project.update');
           // Route::delete('/destroy/{id}', [PartnerController::class,'destroy'])->name('partner.destroy');
           Route::get('/template_excel', [ProjectController::class,'downloadTemplate'])->name('project.downloadTemplate');
           Route::post('/import_excel', [ProjectController::class,'importExcel'])->name('project.import');
           Route::get('/export_excel', [ProjectController::class,'exportExcel'])->name('project.export');
     });

    Route::prefix('approveht')->group(function () {
    // ------------------------------------------------------- Human of Tired --------------------------------------------------------------- \\
        Route::get('/worktripht', [ApproveController::class,'workTripHt'])->name('approveht.worktripHt');
        Route::put('/approveWorktripHt/{id}', [ApproveController::class,'approveWkHt'])->name('approveht.approvedWorkTripHt');
        Route::put('/rejectWorkTripHt/{id}', [ApproveController::class,'rejectWkHt'])->name('approveht.rejectWorokTripHt');

        Route::get('/teleworkht', [ApproveController::class,'teleworkHt'])->name('approveht.teleworkHt');
        Route::put('/approve/TeleWorkHt/{id}', [ApproveController::class,'approveTeleHt'])->name('approveht.approvedTeleHt');
        Route::put('/reject/TeleWorkHt/{id}', [ApproveController::class,'rejectTeleHt'])->name('approveht.rejectTeleHt');

        Route::get('/leaveht', [ApproveController::class,'leaveHt'])->name('approveht.leaveHt');
        Route::put('/approve/LeaveWorkHt/{id}', [ApproveController::class,'approveLeaveHt'])->name('approveht.approvedLeaveHt');
        Route::put('/reject/LeaveWorkHt/{id}', [ApproveController::class,'rejectLeaveHt'])->name('approveht.rejectLeaveHt');
    });

    Route::prefix('approvehr')->group(function () {
        // ------------------------------------------------------- Human Resource --------------------------------------------------------------- \\
        Route::get('/worktriphr', [ApproveController::class,'workTripHumanRes'])->name('approvehr.worktripHr');
        Route::post('/worktriphr/approveWorktripHr/{id}', [ApproveController::class,'approveWkHumanRes'])->name('approvehr.approvedWorkTripHr');
        Route::put('/worktriphr/rejectWorkTripHr/{id}', [ApproveController::class,'rejectWkHumanRes'])->name('approvehr.rejectWorokTripHr');

        Route::get('/teleworkhr', [ApproveController::class,'teleworkHumanRes'])->name('approvehr.teleworkHr');
        Route::put('/teleworkhr/approve/TeleWorkHr/{id}', [ApproveController::class,'approveTeleHumanRes'])->name('approvehr.approvedTeleHr');
        Route::put('/teleworkhr/reject/TeleWorkHr/{id}', [ApproveController::class,'rejectTeleHumanRes'])->name('approvehr.rejectTeleHr');

        Route::get('/leavehr', [ApproveController::class,'leaveHumanRes'])->name('approvehr.leaveHr');
        Route::put('/leavehr/approve/LeaveWorkHr/{id}', [ApproveController::class,'approveLeaveHumanRes'])->name('approvehr.approvedLeaveHr');
        Route::put('/leavehr/reject/LeaveWorkHr/{id}', [ApproveController::class,'rejectLeaveHumanRes'])->name('approvehr.rejectLeaveHr');
    });

    Route::prefix('notification')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notification');
        Route::get('/detail/{id}', [NotificationController::class, 'notificationDetail'])->name('notification.detail');
    });
});

