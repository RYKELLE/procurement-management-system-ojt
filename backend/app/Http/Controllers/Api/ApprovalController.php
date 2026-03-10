<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseRequest;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{

  public function approve($id)
  {
    $purchaseRequest = PurchaseRequest::with('items.item.supplier')->findOrFail($id);

    if ($purchaseRequest->request_status !== 'submitted') {
      return response()->json(['message' => 'Only submitted requests can be approved'], 422);
    }

    $purchaseRequest->update([
      'request_status' => 'approved',
      'approved_by' => Auth::user()->id,
      'date_approved' => now()
    ]);

    $supplierId = $purchaseRequest->items->first()?->item?->supplier_id;

    $total = $purchaseRequest->items->reduce(function ($carry, $item) { //calculate the sum for all items in the request
      return $carry + ($item->item_quantity * $item->unit_price);
    }, 0);

    PurchaseOrder::create([
      'request_id' => $purchaseRequest->id,
      'supplier_id' => $supplierId,
      'created_by' => Auth::user()->id,
      'order_total_amount' => $total,
      'status' => 'active'
    ]);

    return response()->json($purchaseRequest);
  }

  public function reject($id)
  {
    $purchaseRequest = PurchaseRequest::findOrFail($id);

    if ($purchaseRequest->request_status !== 'submitted') {
      return response()->json(['message' => 'Only submitted requests can be approved'], 422);
    }

    $purchaseRequest->update([
      'request_status' => 'rejected',
      'approved_by' => Auth::user()->id,
      'date_approved' => now()
    ]);

    return response()->json($purchaseRequest);
  }
}