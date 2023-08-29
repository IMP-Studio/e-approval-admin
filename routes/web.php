<?php

use App\Http\Controllers\PresenceController;
use App\Http\Controllers\divisionController;
use App\Http\Controllers\employeeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\roleController;
use App\Http\Controllers\superAdminController;
use App\Models\employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Auth::routes();
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/cuti', [HomeController::class, 'cuti'])->name('cuti');
    Route::get('/standup', [HomeController::class, 'standup'])->name('standup');

    Route::get('/attendance', [PresenceController::class, 'index'])->name('kehadiran');

    Route::prefix('divisi')->group(function () {
        Route::get('/', [divisionController::class, 'index'])->name('divisi');
        Route::get('/create', [divisionController::class, 'create'])->name('divisi.create');
        Route::post('/store', [divisionController::class, 'store'])->name('divisi.store');
        Route::get('/edit/{id}', [divisionController::class, 'edit'])->name('divisi.edit');
        Route::put('/update/{id}', [divisionController::class, 'update'])->name('divisi.update');
        Route::delete('/destroy/{id}', [divisionController::class, 'destroy'])->name('divisi.destroy');
        Route::get('/export_excel', [divisionController::class,'export_excel'])->name('division.excel');
        Route::post('/import', [divisionController::class,'import_excel'])->name('division.import');
    });

    Route::prefix('position')->group(function () {
        Route::get('/', [PositionController::class, 'index'])->name('position');
        Route::post('/store', [PositionController::class, 'store'])->name('position.store');
        Route::put('/update/{id}', [PositionController::class, 'update'])->name('position.update');
        Route::delete('/destroy/{id}', [PositionController::class, 'destroy'])->name('position.destroy');
    });

    Route::prefix('employee')->group(function () {
        Route::get('/', [employeeController::class,'index'])->name('employee');
        Route::get('/create', [employeeController::class,'create'])->name('employee.create');
        Route::post('/store', [employeeController::class,'store'])->name('employee.store');
        Route::get('/edit/{id}', [employeeController::class,'edit'])->name('employee.edit');
        Route::put('/update/{id}', [employeeController::class,'update'])->name('employee.update');
        Route::delete('/destroy/{id}', [employeeController::class,'destroy'])->name('employee.destroy');
        Route::get('/trash', [employeeController::class,'trash'])->name('employee.trash');
        Route::get('/export_excel', [employeeController::class,'export_excel'])->name('employee.excel');
        Route::get('/export_pdf', [employeeController::class,'export_pdf'])->name('employee.pdf');
        Route::post('/import_excel', [employeeController::class,'import_excel'])->name('employee.import');
    });
});

