<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
  public function index()
  {
    return response()->json(
      Supplier::query()->latest()->get()
    );
  }

  public function store(Request $request)
  {
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
    $validated = $request->validate([
      'supplier_name' => ['sometimes', 'string', 'max:255'],
      'contact' => ['sometimes', 'nullable', 'string', 'max:255'],
      'email' => ['sometimes', 'nullable', 'email', 'max:255'],
    ]);

    $supplier->update($validated);

    return response()->json($supplier);
  }
}
