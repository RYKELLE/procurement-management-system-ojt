<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
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
}