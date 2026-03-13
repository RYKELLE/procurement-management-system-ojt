<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderController extends Controller
{
  public function index()
  {
    $user = Auth::user(); //get the current user
    if (!$user || !$user->can('view-purchase-orders')) {
      return response()->json(['message' => 'Access Denied'], 403);
    }

    if ($user->can('view-all-purchase-requests') || $user->can('manage-purchase-orders')) {
      $orders = PurchaseOrder::with('purchaseRequest.requester', 'supplier')
        ->latest()
        ->get();
    } else {
      $orders = PurchaseOrder::with('purchaseRequest.requester', 'supplier')
        ->whereHas('purchaseRequest', function ($query) use ($user) {
          $query->where('requested_by', $user->id);
        })
        ->latest()
        ->get();
    }

    return response()->json($orders);
  }

  public function show($id)
  {
    $user = Auth::user();
    if (!$user || !$user->can('view-purchase-orders')) {
      return response()->json(['message' => 'Access Denied'], 403);
    }

    $order = PurchaseOrder::with('purchaseRequest.items.item', 'purchaseRequest.requester', 'supplier', 'invoice')
      ->findOrFail($id);

    if (!($user->can('view-all-purchase-requests') || $user->can('manage-purchase-orders')) && $order->purchaseRequest->requested_by !== $user->id) {
      return response()->json(['message' => 'Access Denied'], 403);
    }

    return response()->json($order);
  }

  public function markAsCompleted($id)
  {
    $user = Auth::user();
    if (!$user || !$user->can('manage-purchase-orders')) {
      return response()->json(['message' => 'Access Denied'], 403);
    }

    $purchaseOrder = PurchaseOrder::with('purchaseRequest.items')
      ->findOrFail($id);

    if ($purchaseOrder->status !== 'active') {
      return response()->json(['message' => 'Only active orders can be marked as paid'], 422);
    }

    $purchaseOrder->update(['status' => 'completed']);

    $invoice = Invoice::create([
      'order_id' => $purchaseOrder->id,
      'processed_by' => $user->id,
      'status' => 'unpaid',
      'amount' => $purchaseOrder->order_total_amount,
      'due_date' => now()->addDays(15),
    ]);

    return response()->json([
      'purchase_order' => $purchaseOrder,
      'invoice' => $invoice,
    ]);
  }
}
