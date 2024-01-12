<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\SetPermissionRequest;
use App\Repositories\PermissionRepository;
use App\Repositories\ModelPermissionRepository;
use App\Repositories\ModelRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    protected $permissionRepository;
    protected $modelRepository;
    protected $modelpermissionRepository;

    public function __construct(
        PermissionRepository $permissionRepository,
        ModelRepository $modelRepository,
        ModelPermissionRepository $modelpermissionRepository
    ) {
        $this->permissionRepository = $permissionRepository;
        $this->modelRepository = $modelRepository;
        $this->modelpermissionRepository = $modelpermissionRepository;
    }

    /**
     * Show the index page.
     *
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
{
    $adminCount = User::whereHas('roles', function ($query) {
        $query->where('name', 'super-admin');
    })->count();

    $hrCount = User::whereHas('permissions', function ($query) {
        $query->whereIn('id', [39, 43]);
    })->count();

    $htCount = User::whereHas('permissions', function ($query) {
        $query->whereIn('id', [38, 42]);
    })->count();

    $ordinaryEmployeeCount = User::whereHas('roles', function ($query) {
            $query->where('name', 'employee')
                ->whereNotIn('id', function ($subQuery) {
                    $subQuery->select('model_id')
                        ->from('model_has_permissions');
                });
        })->count();

    $employees = User::whereHas('roles', function ($query) {
            $query->where('name', 'employee');
        })
        ->with(['employee', 'employee.division'])
        ->get();

    $permissions = $this->permissionRepository->all();

    $roleName = $request->has('name') && $request->name != "" ? $request->name : 'employee';
    $roleSelected = $roleName === 'employee' ? 'employee' : null;
    $hasPermissions = null;

    // Assuming you have a logged-in user and you want to get their permissions
    $loggedInUser = auth()->user();

    if ($loggedInUser) {
        $hasPermissions = $loggedInUser->permissions()->pluck('name')->toArray();
    }

    return view('permission.index', compact('employees', 'permissions', 'roleName', 'roleSelected', 'hasPermissions', 'adminCount', 'hrCount', 'htCount', 'ordinaryEmployeeCount'));
}


public function setModelPermissions(SetPermissionRequest $request, $userId)
{
    DB::beginTransaction();
    try {
        $type = 'User';  // Change to 'User'
        $id = $userId;  // Assuming you send the ID as 'user_id' in the request

        $model = $this->modelRepository->findModelById($type, $id);

        if (!$model) {
            return response()->json(['message' => 'Model not found'], 404);
        }

        // Sync permissions
        $model->permissions()->sync($request->input('permissions', []));

        $response = [
            'model_type' => $type,
            'model_id' => $id,
            'permission_id' => $model->permissions->pluck('id')->toArray()
        ];

        DB::commit();
        return response()->json(['message' => 'User permissions updated successfully', 'data' => $response], 200);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


    public function getUserPermissions($userId)
    {
        $user = User::find($userId);

        // Assuming `permissions` is a relationship defined in the User model.
        $directPermissions = $user->permissions;
        $permissionsViaRoles = $user->getAllPermissions();
        $allPermissions = $directPermissions->merge($permissionsViaRoles);
        $permissionNames = $allPermissions->pluck('name');

        return response()->json($permissionNames, 200);
    }
}
