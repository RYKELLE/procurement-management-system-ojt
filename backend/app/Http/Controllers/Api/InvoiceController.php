<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Auth;
use App\Models\Invoice;
class InvoiceController extends Controller
{
  public function index()
  {
    $user = Auth::user();

    if ($user->hasRole('staff')) {
      $invoices = Invoice::with('purchaseOrder.purchaseRequest.requester', 'processor')
        ->whereHas('purchaseOrder.purchaseRequest', function ($query) use ($user) {
          $query->where('requested_by', $user->id);
        })
        ->latest()
        ->get();
    } else {
      $invoices = Invoice::with('purchaseOrder.purchaseRequest.requester', 'processor')
        ->latest()
        ->get();
    }

    return response()->json($invoices);
  }

  public function show($id)
  {
    $user = Auth::user();

    $invoice = Invoice::with('purchaseOrder.purchaseRequest.items.item', 'processor')
      ->findOrFail($id);

    if ($user->hasRole('staff') && $invoice->purchaseOrder->purchaseRequest->requested_by !== $user->id) {
      return response()->json(['message' => 'Unauthorized'], 403);
    }

    return response()->json($invoice);
  }

  public function markAsPaid($id)
  {
    $invoice = Invoice::findOrFail($id);

    if ($invoice->status !== 'unpaid') {
      return response()->json(['message' => 'Only unpaid invoices can be marked as paid'], 422);
    }

    $invoice->update(['status' => 'paid']);

    return response()->json($invoice);
  }
}