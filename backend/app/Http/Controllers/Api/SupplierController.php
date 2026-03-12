<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
  public function index()
  {
    $actor = Auth::user();
    if (!$actor || (!$actor->can('view-suppliers') && !$actor->can('manage-suppliers'))) {
      return response()->json(['message' => 'Unauthorized'], 403);
    }

    return response()->json(
      Supplier::query()->latest()->get()
    );
  }

  public function store(Request $request)
  {
    $actor = Auth::user();
    if (!$actor || !$actor->can('manage-suppliers')) {
      return response()->json(['message' => 'Unauthorized'], 403);
    }

    $validated = $request->validate([
      'supplier_name' => ['required', 'string', 'max:255'],
      'contact' => ['nullable', 'string', 'max:255'],
      'email' => ['nullable', 'email', 'max:255'],
    ]);

    $supplier = Supplier::create($validated);

    return response()->json($supplier, 201);
  }

  public function update(Request $request, Supplier $supplier)
  {
    $actor = Auth::user();
    if (!$actor || !$actor->can('manage-suppliers')) {
      return response()->json(['message' => 'Unauthorized'], 403);
    }

    $validated = $request->validate([
      'supplier_name' => ['sometimes', 'string', 'max:255'],
      'contact' => ['sometimes', 'nullable', 'string', 'max:255'],
      'email' => ['sometimes', 'nullable', 'email', 'max:255'],
    ]);

    $supplier->update($validated);

    return response()->json($supplier);
  }
}
