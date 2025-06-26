<?php

namespace App\Modules\Membership\Services;

use App\Modules\Membership\Repositories\MembershipRepository;
use Illuminate\Support\Facades\Config;

class PointService
{
    protected $membershipRepository;

    public function __construct(MembershipRepository $membershipRepository)
    {
        $this->membershipRepository = $membershipRepository;
    }

    public function awardPointsForCourseBooking(int $userId): void
    {
        $points = Config::get('points.rules.course_booking');
        $this->membershipRepository->addPoints($userId, $points);
    }

    public function awardPointsForCourseAttendance(int $userId): void
    {
        $points = Config::get('points.rules.course_attendance');
        $this->membershipRepository->addPoints($userId, $points);
    }

    public function awardPointsForPayment(int $userId, float $amount): void
    {
        $pointsPer100 = Config::get('points.rules.payment_per_100');
        $points = floor($amount / 100) * $pointsPer100;
        if ($points > 0) {
            $this->membershipRepository->addPoints($userId, $points);
        }
    }

    public function awardPointsForReferral(int $referrerId): void
    {
        $points = Config::get('points.rules.referral');
        $this->membershipRepository->addPoints($referrerId, $points);
    }

    public function getMemberPoints(int $userId): int
    {
        return $this->membershipRepository->getMemberTier($userId)?->points ?? 0;
    }
}
