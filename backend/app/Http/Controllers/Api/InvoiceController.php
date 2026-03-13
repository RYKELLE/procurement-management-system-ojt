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
    if (!$user || !$user->can('view-invoices')) {
      return response()->json(['message' => 'Access Denied'], 403);
    }

    if ($user->can('view-all-purchase-requests') || $user->can('manage-invoices')) {
      $invoices = Invoice::with('purchaseOrder.purchaseRequest.requester', 'processor')
        ->latest()
        ->get();
    } else {
      $invoices = Invoice::with('purchaseOrder.purchaseRequest.requester', 'processor')
        ->whereHas('purchaseOrder.purchaseRequest', function ($query) use ($user) {
          $query->where('requested_by', $user->id);
        })
        ->latest()
        ->get();
    }

    return response()->json($invoices);
  }

  public function show($id)
  {
    $user = Auth::user();
    if (!$user || !$user->can('view-invoices')) {
      return response()->json(['message' => 'Access Denied'], 403);
    }

    $invoice = Invoice::with('purchaseOrder.purchaseRequest.items.item', 'processor')
      ->findOrFail($id);

    if (!($user->can('view-all-purchase-requests') || $user->can('manage-invoices')) && $invoice->purchaseOrder->purchaseRequest->requested_by !== $user->id) {
      return response()->json(['message' => 'Access Denied'], 403);
    }

    return response()->json($invoice);
  }

  public function markAsPaid($id)
  {
    $user = Auth::user();
    if (!$user || !$user->can('manage-invoices')) {
      return response()->json(['message' => 'Access Denied'], 403);
    }

    $invoice = Invoice::findOrFail($id);

    if ($invoice->status !== 'unpaid') {
      return response()->json(['message' => 'Only unpaid invoices can be marked as paid'], 422);
    }

    $invoice->update(['status' => 'paid']);

    return response()->json($invoice);
  }
}
