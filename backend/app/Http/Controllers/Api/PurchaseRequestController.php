<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use App\Models\RequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseRequestController extends Controller
{
  public function index()
  {
    $user = Auth::user(); //gets the current user

    if ($user->hasRole("staff")) {
      $request = PurchaseRequest::with('items.item', 'requester')
        ->where('requested_by', $user->id)
        ->latest()
        ->get();
    } else {
      $request = PurchaseRequest::with('items.item', 'requester', 'approver')
        ->latest()
        ->get();
    }

    return response()->json($request);
  }

  public function store(Request $request)
  {
    $validate = $request->validate([ //validate the user input for a new request
      'reason' => 'required|string',
      'items' => 'required|array|min:1', //takes an array
      'items.*.item_id' => 'required|exists:items,id',
      'items.*.item_quantity' => 'required|integer|min:1',
      'items.*.unit_price' => 'required|numeric|min:0'
    ]);

    $purchaseRequest = PurchaseRequest::create([ //creating the request itself
      'requested_by' => Auth::user()->id,
      'reason' => $validate['reason'],
      'request_status' => 'draft'
    ]);

    foreach ($validate['items'] as $item) { //creates a Request item for each item included in the request
      RequestItem::create([
        'purchase_request_id' => $purchaseRequest->id,
        'item_id' => $item['item_id'],
        'item_quantity' => $item['item_quantity'],
        'unit_price' => $item['unit_price']
      ]);
    }

    return response()->json($purchaseRequest->load('items.item'), 201);
  }

  public function show($id)
  {
    $user = Auth::user(); //get the current user 

    $purchaseRequest = PurchaseRequest::with('items.item', 'approver', 'requester') //finds the specific request using the request ID
      ->findOrFail($id);

    if ($user->hasRole('staff') && $purchaseRequest->requested_by !== $user->id) { //validates that staff are only able to access their own requests 
      return response()->json(['message' => 'Unauthorized'], 403);
    }

    return response()->json($purchaseRequest);
  }

  public function submit($id)
  {
    $user = Auth::user();

    $purchaseRequest = PurchaseRequest::findOrFail($id);

    if ($purchaseRequest->requested_by !== $user->id) { //u can only submit user own requests
      return response()->json(['message' => 'Unauthrized'], 403);
    }

    if ($purchaseRequest->request_status !== 'draft') {
      return response()->json(['message' => 'You can only submit a request that is a draft'], 422);
    }

    $purchaseRequest->update(['request_status' => 'submitted']); //update the status of the request

    return response()->json($purchaseRequest);
  }
}

