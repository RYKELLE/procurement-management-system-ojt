<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseRequest;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{

  public function approve($id)
  {
    $purchaseRequest = PurchaseRequest::findOrFail($id);

    if ($purchaseRequest->request_status !== 'submitted') {
      return response()->json(['message' => 'Only submitted requests can be approved'], 422);
    }

    $purchaseRequest->update([
      'request_status' => 'approved',
      'approved_by' => Auth::user()->id,
      'date_approved' => now()
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