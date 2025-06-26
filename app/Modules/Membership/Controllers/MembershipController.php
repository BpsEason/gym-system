<?php

namespace App\Modules\Membership\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Membership\Repositories\MembershipRepository;
use App\Modules\Membership\Services\PointService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MembershipController extends Controller
{
    protected $membershipRepository;
    protected $pointService;

    public function __construct(MembershipRepository $membershipRepository, PointService $pointService)
    {
        $this->membershipRepository = $membershipRepository;
        $this->pointService = $pointService;
    }

    /**
     * Get authenticated user's membership tier and points.
     */
    public function getMyMembership(Request $request): JsonResponse
    {
        $memberId = Auth::id();
        $memberTier = $this->membershipRepository->getMemberTier($memberId);

        if (!$memberTier) {
            return response()->json(['message' => 'Membership data not found for this user.'], 404);
        }

        return response()->json([
            'member_id' => $memberTier->member_id,
            'tier_name' => $memberTier->tier_name,
            'points' => $memberTier->points,
            'expires_at' => $memberTier->expires_at ? $memberTier->expires_at->toDateTimeString() : null,
        ]);
    }

    /**
     * Award points to a member (Admin only or system event).
     */
    public function addPoints(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'points' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ]);

        // Authorization check (e.g., only admin can manually add points)
        $this->authorize('addPoints', User::find($request->user_id));

        $memberTier = $this->pointService->awardPointsForCourseBooking($request->user_id); // Re-using method for example
        // In a real scenario, you'd have more specific methods for different reasons
        // $this->pointService->awardPoints($request->user_id, $request->points, $request->reason);

        return response()->json([
            'message' => 'Points added successfully.',
            'member_tier' => $memberTier->toArray(),
        ]);
    }
}
