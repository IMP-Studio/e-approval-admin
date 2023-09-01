<?php

use App\Http\Controllers\DivisionController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\roleController;
use App\Http\Controllers\superAdminController;
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
        Route::get('/', [DivisionController::class, 'index'])->name('divisi');
        Route::get('/create', [DivisionController::class, 'create'])->name('divisi.create');
        Route::post('/store', [DivisionController::class, 'store'])->name('divisi.store');
        Route::get('/edit/{id}', [DivisionController::class, 'edit'])->name('divisi.edit');
        Route::put('/update/{id}', [DivisionController::class, 'update'])->name('divisi.update');
        Route::delete('/destroy/{id}', [DivisionController::class, 'destroy'])->name('divisi.destroy');
        Route::get('/export_excel', [DivisionController::class,'export_excel'])->name('division.excel');
        Route::post('/import', [DivisionController::class,'import_excel'])->name('division.import');
    });

    Route::prefix('position')->group(function () {
        Route::get('/', [PositionController::class, 'index'])->name('position');
        Route::post('/store', [PositionController::class, 'store'])->name('position.store');
        Route::put('/update/{id}', [PositionController::class, 'update'])->name('position.update');
        Route::delete('/destroy/{id}', [PositionController::class, 'destroy'])->name('position.destroy');
    });

    Route::prefix('employee')->group(function () {
        Route::get('/', [EmployeeController::class,'index'])->name('employee');
        Route::get('/create', [EmployeeController::class,'create'])->name('employee.create');
        Route::post('/store', [EmployeeController::class,'store'])->name('employee.store');
        Route::get('/edit/{id}', [EmployeeController::class,'edit'])->name('employee.edit');
        Route::put('/update/{id}', [EmployeeController::class,'update'])->name('employee.update');
        Route::delete('/destroy/{id}', [EmployeeController::class,'destroy'])->name('employee.destroy');
        Route::get('/trash', [EmployeeController::class,'trash'])->name('employee.trash');
        Route::get('/export_excel', [EmployeeController::class,'export_excel'])->name('employee.excel');
        Route::get('/export_pdf', [EmployeeController::class,'export_pdf'])->name('employee.pdf');
        Route::post('/import_excel', [EmployeeController::class,'import_excel'])->name('employee.import');
        Route::get('/get-positions/{id}', [EmployeeController::class, 'getPositions']);    });
});

