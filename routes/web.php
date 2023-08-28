<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\divisiController;
use App\Http\Controllers\divisionController;
use App\Http\Controllers\employeeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PosisiController;
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

    Route::get('/attendance', [AbsensiController::class, 'index'])->name('kehadiran');

    Route::prefix('divisi')->group(function () {
        Route::get('/', [divisiController::class, 'index'])->name('divisi');
        Route::get('/create', [divisiController::class, 'create'])->name('divisi.create');
        Route::post('/store', [divisiController::class, 'store'])->name('divisi.store');
        Route::get('/edit/{id}', [divisiController::class, 'edit'])->name('divisi.edit');
        Route::put('/update/{id}', [divisiController::class, 'update'])->name('divisi.update');
        Route::delete('/destroy/{id}', [divisiController::class, 'destroy'])->name('divisi.destroy');
        Route::get('/export_excel', [divisiController::class,'export_excel'])->name('division.excel');
        Route::post('/import', [divisiController::class,'import_excel'])->name('division.import');
    });

    Route::prefix('position')->group(function () {
        Route::get('/', [PosisiController::class, 'index'])->name('position');
        Route::post('/store', [PosisiController::class, 'store'])->name('position.store');
        Route::put('/update/{id}', [PosisiController::class, 'update'])->name('position.update');
        Route::delete('/destroy/{id}', [PosisiController::class, 'destroy'])->name('position.destroy');
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

