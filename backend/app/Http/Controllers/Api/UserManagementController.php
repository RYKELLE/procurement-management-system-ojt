<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
  public function index()
  {
    $actor = Auth::user();
    if (!$actor || !$actor->can('manage-users')) {
      return response()->json(['message' => 'Unauthorized'], 403);
    }

    $users = User::query()
      ->with(['roles', 'permissions'])
      ->orderBy('id')
      ->get()
      ->map(function (User $user) {
        return [
          'id' => $user->id,
          'name' => $user->name,
          'email' => $user->email,
          'role' => strtolower((string) $user->getRoleNames()->first()),
          // Effective permissions (direct + via role, minus per-user revocations)
          'permissions' => $user->effectivePermissionNames(),
        ];
      })
      ->values();

    return response()->json($users);
  }

  public function store(Request $request)
  {
    $actor = Auth::user();
    if (!$actor || !$actor->can('manage-users')) {
      return response()->json(['message' => 'Unauthorized'], 403);
    }

    $rolesTable = config('permission.table_names.roles') ?? 'roles';

    $validated = $request->validate([
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'email', 'max:255', 'unique:users,email'],
      'password' => ['required', 'string', 'min:8'],
      'role' => ['required', 'string', Rule::exists($rolesTable, 'name')],
    ]);

    $user = User::create([
      'name' => $validated['name'],
      'email' => $validated['email'],
      'password' => Hash::make($validated['password']),
    ]);

    $roleName = strtolower($validated['role']);
    if ($roleName !== '') {
      $role = Role::findByName($roleName, 'web');
      $user->syncRoles([$role]);
    }

    return response()->json([
      'id' => $user->id,
      'name' => $user->name,
      'email' => $user->email,
      'role' => strtolower((string) $user->getRoleNames()->first()),
      'permissions' => $user->effectivePermissionNames(),
    ], 201);
  }

  public function updateRole(Request $request, User $user)
  {
    $actor = Auth::user();
    if (!$actor || !$actor->can('manage-users')) {
      return response()->json(['message' => 'Unauthorized'], 403);
    }

    $rolesTable = config('permission.table_names.roles') ?? 'roles';

    $validated = $request->validate([
      'role' => ['required', 'string', Rule::exists($rolesTable, 'name')],
    ]);

    $roleName = strtolower($validated['role']);
    $role = Role::findByName($roleName, 'web');
    $user->syncRoles([$role]);

    return response()->json([
      'id' => $user->id,
      'name' => $user->name,
      'email' => $user->email,
      'role' => strtolower((string) $user->getRoleNames()->first()),
      'permissions' => $user->effectivePermissionNames(),
    ]);
  }

  public function updatePermissions(Request $request, User $user)
  {
    $actor = Auth::user();
    if (!$actor || !$actor->can('manage-users')) {
      return response()->json(['message' => 'Unauthorized'], 403);
    }

    $permissionsTable = config('permission.table_names.permissions') ?? 'permissions';

    $validated = $request->validate([
      'permissions' => ['present', 'array'],
      'permissions.*' => ['string', Rule::exists($permissionsTable, 'name')],
    ]);

    $desired = array_values(array_unique(array_map('strval', $validated['permissions'])));

    $rolePermissions = $user->getPermissionsViaRoles()->pluck('name')->map(fn ($n) => (string) $n)->values()->all();

    // Only permissions NOT provided by role should be stored as direct user permissions.
    $direct = array_values(array_diff($desired, $rolePermissions));

    // If a role provides a permission but it's NOT in the desired list, treat it as revoked for this user.
    $revokedNames = array_values(array_diff($rolePermissions, $desired));

    $user->syncPermissions($direct);

    $revokedIds = Permission::query()
      ->whereIn('name', $revokedNames)
      ->pluck('id')
      ->values()
      ->all();

    $user->revokedPermissions()->sync($revokedIds);

    return response()->json([
      'id' => $user->id,
      'name' => $user->name,
      'email' => $user->email,
      'role' => strtolower((string) $user->getRoleNames()->first()),
      'permissions' => $user->effectivePermissionNames(),
    ]);
  }

  public function destroy(User $user)
  {
    $actor = Auth::user();
    if (!$actor || !$actor->can('manage-users')) {
      return response()->json(['message' => 'Unauthorized'], 403);
    }

    if ($actor->id === $user->id) {
      return response()->json(['message' => 'You cannot delete your own account.'], 422);
    }

    $user->delete();

    return response()->json(['message' => 'Deleted']);
  }
}
