<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\PurchaseRequest;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) return response()->json(['message' => 'Access Denied'], 403);

        // ── Stats ────────────────────────────────────────────
        $pendingCount = $user->can('view-all-purchase-requests')
            ? PurchaseRequest::where('request_status', 'submitted')->count()
            : PurchaseRequest::where('requested_by', $user->id)->where('request_status', 'submitted')->count();

        $supplierCount = Supplier::count();

        // ── Recent Activity ──────────────────────────────────
        $activity = ActivityLog::with('user')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($log) => [
                'id'          => $log->id,
                'description' => $log->description,
                'subject_type'=> $log->subject_type,
                'subject_id'  => $log->subject_id,
                'user'        => $log->user?->name ?? 'System',
                'created_at'  => $log->created_at->diffForHumans(),
            ]);

        return response()->json([
            'pending_requests' => $pendingCount,
            'total_suppliers'  => $supplierCount,
            'recent_activity'  => $activity,
        ]);
    }
}
