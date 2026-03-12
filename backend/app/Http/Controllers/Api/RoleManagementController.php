<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RoleManagementController extends Controller
{
  public function index()
  {
    $actor = Auth::user();
    if (!$actor || !$actor->can('manage-roles')) {
      return response()->json(['message' => 'Unauthorized'], 403);
    }

    $roles = Role::query()
      ->with('permissions')
      ->orderBy('id')
      ->get()
      ->map(function (Role $role) {
        $key = strtolower($role->name);
        $name = ucfirst($key);

        return [
          'key' => $key,
          'name' => $name,
          'permissions' => $role->permissions->pluck('name')->values()->all(),
        ];
      })
      ->values();

    return response()->json($roles);
  }

  public function updatePermissions(Request $request, string $role)
  {
    $actor = Auth::user();
    if (!$actor || !$actor->can('manage-roles')) {
      return response()->json(['message' => 'Unauthorized'], 403);
    }

    $permissionsTable = config('permission.table_names.permissions') ?? 'permissions';

    $validated = $request->validate([
      'permissions' => ['present', 'array'],
      'permissions.*' => ['string', Rule::exists($permissionsTable, 'name')],
    ]);

    $roleModel = Role::findByName(strtolower($role), 'web');
    $roleModel->syncPermissions($validated['permissions']);

    return response()->json([
      'key' => strtolower($roleModel->name),
      'name' => ucfirst(strtolower($roleModel->name)),
      'permissions' => $roleModel->permissions()->pluck('name')->values()->all(),
    ]);
  }
}

