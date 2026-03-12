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

    if ($user->hasRole('staff')) {
      $orders = PurchaseOrder::with('purchaseRequest.requester', 'supplier')
        ->whereHas('purchaseRequest', function ($query) use ($user) {
          $query->where('requested_by', $user->id);
        })
        ->latest()
        ->get();
    } else {
      $orders = PurchaseOrder::with('purchaseRequest.requester', 'supplier')
        ->latest()
        ->get();
    }

    return response()->json($orders);
  }

  public function show($id)
  {
    $user = Auth::user();

    $order = PurchaseOrder::with('purchaseRequest.items.item', 'purchaseRequest.requester', 'supplier')
      ->findOrFail($id);

    if ($user->hasRole('staff') && $order->purchaseRequest->requested_by !== $user->id) {
      return response()->json(['message' => 'Unauthorized'], 403);
    }

    return response()->json($order);
  }

  public function markAsCompleted($id)
  {
    $purchaseOrder = PurchaseOrder::with('purchaseRequest.items')
      ->findOrFail($id);

    if ($purchaseOrder->status !== 'active') {
      return response()->json(['message' => 'Only active orders can be marked as paid'], 422);
    }

    $purchaseOrder->update(['status' => 'completed']);

    Invoice::create([
      'order_id' => $purchaseOrder->id,
      'processed_by' => Auth::user()->id,
      'status' => 'unpaid',
      'order_total_amount' => $purchaseOrder->amount,
      'due_date' => now()->addDays(15),
    ]);

    return response()->json($purchaseOrder);
  }
}