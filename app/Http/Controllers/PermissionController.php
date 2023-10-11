<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
{
    $employees = User::whereHas('roles', function ($query) {
            $query->where('name', 'employee');
        })
        ->with(['employee', 'employee.division'])
        ->get();
    
    return view('permission.index', compact('employees'));
}

}
