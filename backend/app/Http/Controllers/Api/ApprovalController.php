<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseRequest;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{

  private function log(string $description, PurchaseRequest $subject): void
    {
        $alias = 'PR-' . str_pad((string) $subject->id, 5, '0', STR_PAD_LEFT);

        ActivityLog::create([
            'user_id'      => Auth::id(),
            'description'  => str_replace('{request}', $alias, $description),
            'subject_type' => 'purchase_request',
            'subject_id'   => $subject->id,
        ]);
    }

  public function approve($id)
  {
    $actor = Auth::user();
    if (!$actor || !$actor->can('approve-purchase-request')) {
      return response()->json(['message' => 'Access Denied'], 403);
    }

    $purchaseRequest = PurchaseRequest::with('items.item.supplier')->findOrFail($id);

    if ($purchaseRequest->request_status !== 'submitted') {
      return response()->json(['message' => 'Only submitted requests can be approved'], 422);
    }

    $purchaseRequest->update([
      'request_status' => 'approved',
      'approved_by' => $actor->id,
      'date_approved' => now()
    ]);

    $supplierId = $purchaseRequest->items->first()?->item?->supplier_id;

    $total = $purchaseRequest->items->reduce(function ($carry, $item) { //calculate the sum for all items in the request
      return $carry + ($item->item_quantity * $item->unit_price);
    }, 0);

    PurchaseOrder::create([
      'request_id' => $purchaseRequest->id,
      'supplier_id' => $supplierId,
      'created_by' => $actor->id,
      'order_total_amount' => $total,
      'status' => 'active'
    ]);

    $this->log("Purchase request {request} approved", $purchaseRequest);

    return response()->json($purchaseRequest);
  }

  public function reject($id)
  {
    $actor = Auth::user();
    if (!$actor || !$actor->can('reject-purchase-request')) {
      return response()->json(['message' => 'Access Denied'], 403);
    }

    $purchaseRequest = PurchaseRequest::findOrFail($id);

    if ($purchaseRequest->request_status !== 'submitted') {
      return response()->json(['message' => 'Only submitted requests can be approved'], 422);
    }

    $purchaseRequest->update([
      'request_status' => 'rejected',
      'approved_by' => $actor->id,
      'date_approved' => now()
    ]);

    $this->log("Purchase request {request} rejected", $purchaseRequest);

    return response()->json($purchaseRequest);
  }
}
