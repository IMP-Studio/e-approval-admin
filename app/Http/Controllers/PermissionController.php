<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        // Get employees with their divisions
        $employees = Employee::with('division','user')->get();

        
        return view('permission.index', compact('employees'));
    }
}
