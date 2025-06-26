<?php

namespace App\Modules\Course\Services;

use App\Modules\Course\Repositories\CourseRepository;
use App\Modules\Membership\Services\PointService; # Dependency
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class CourseService
{
    protected $courseRepository;
    protected $pointService;

    public function __construct(CourseRepository $courseRepository, PointService $pointService)
    {
        $this->courseRepository = $courseRepository;
        $this->pointService = $pointService;
    }

    /**
     * Book a course for a member.
     *
     * @param int $memberId
     * @param int $scheduleId
     * @return array
     */
    public function bookCourse(int $memberId, int $scheduleId): array
    {
        $schedule = $this->courseRepository->getCourseScheduleById($scheduleId);

        if (!$schedule) {
            return ['success' => false, 'message' => 'Course schedule not found.'];
        }

        if ($schedule->booked_count >= $schedule->course->max_capacity) {
            // Option 1: Prevent booking if full and suggest waitlist
            return ['success' => false, 'message' => 'Course is full. Consider joining the waitlist.'];
            // Option 2: Automatically add to waitlist
            // return $this->addToWaitlist($memberId, $scheduleId);
        }

        // Check for duplicate booking
        if ($this->courseRepository->getBookingById($memberId, $scheduleId)) { // Assuming getBookingById can take member_id, schedule_id
            return ['success' => false, 'message' => 'You have already booked this course.'];
        }

        DB::beginTransaction();
        try {
            $booking = $this->courseRepository->createBooking([
                'member_id' => $memberId,
                'course_schedule_id' => $scheduleId,
                'booking_status' => 'confirmed',
                'points_earned' => 0, // Points are awarded after successful booking
            ]);

            $schedule->increment('booked_count');
            $this->pointService->awardPointsForCourseBooking($memberId);

            DB::commit();
            Log::info("Member ID: {$memberId} booked course schedule ID: {$scheduleId}.");
            return ['success' => true, 'message' => 'Course booked successfully.', 'booking' => $booking];

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Failed to book course for member ID: {$memberId}, schedule ID: {$scheduleId}. Error: {$e->getMessage()}");
            return ['success' => false, 'message' => 'Booking failed: ' . $e->getMessage()];
        }
    }

    /**
     * Cancel a course booking.
     *
     * @param int $bookingId
     * @param int $memberId (for authorization)
     * @return array
     */
    public function cancelBooking(int $bookingId, int $memberId): array
    {
        $booking = $this->courseRepository->getBookingById($bookingId);

        if (!$booking || $booking->member_id !== $memberId) {
            return ['success' => false, 'message' => 'Booking not found or unauthorized.'];
        }

        if ($booking->booking_status === 'cancelled') {
            return ['success' => false, 'message' => 'Booking is already cancelled.'];
        }

        $cancellationWindowHours = Config::get('course.cancellation_window_hours');
        $schedule = $booking->schedule;

        if ($schedule->start_at->diffInHours(Carbon::now()) < $cancellationWindowHours) {
            return ['success' => false, 'message' => "Cannot cancel. Cancellation window closed ({$cancellationWindowHours} hours before start)."];
        }

        DB::beginTransaction();
        try {
            $booking->update(['booking_status' => 'cancelled']);
            $schedule->decrement('booked_count');

            // Optionally, penalize points or refund points if applicable
            // Re-fill from waitlist if any
            $this->processWaitlistForVacancy($schedule->id);

            DB::commit();
            Log::info("Booking ID: {$bookingId} for member ID: {$memberId} cancelled.");
            return ['success' => true, 'message' => 'Booking cancelled successfully.'];

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Failed to cancel booking ID: {$bookingId}. Error: {$e->getMessage()}");
            return ['success' => false, 'message' => 'Cancellation failed: ' . $e->getMessage()];
        }
    }

    /**
     * Add a member to a course waitlist.
     *
     * @param int $memberId
     * @param int $scheduleId
     * @return array
     */
    public function addToWaitlist(int $memberId, int $scheduleId): array
    {
        $schedule = $this->courseRepository->getCourseScheduleById($scheduleId);

        if (!$schedule) {
            return ['success' => false, 'message' => 'Course schedule not found.'];
        }

        // Prevent adding to waitlist if course is not full
        if ($schedule->booked_count < $schedule->course->max_capacity) {
            return ['success' => false, 'message' => 'Course is not full. You can book directly.'];
        }

        // Check for existing waitlist entry
        if ($this->courseRepository->getWaitlistEntry($memberId, $scheduleId)) {
            return ['success' => false, 'message' => 'You are already on the waitlist for this course.'];
        }

        DB::beginTransaction();
        try {
            $lastPosition = Waitlist::where('course_schedule_id', $scheduleId)->max('position') ?? 0;
            $waitlistEntry = $this->courseRepository->addMemberToWaitlist($memberId, $scheduleId, $lastPosition + 1);

            DB::commit();
            Log::info("Member ID: {$memberId} added to waitlist for schedule ID: {$scheduleId}.");
            return ['success' => true, 'message' => 'Added to waitlist successfully.', 'waitlist_entry' => $waitlistEntry];

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Failed to add member ID: {$memberId} to waitlist for schedule ID: {$scheduleId}. Error: {$e->getMessage()}");
            return ['success' => false, 'message' => 'Adding to waitlist failed: ' . $e->getMessage()];
        }
    }

    /**
     * Process waitlist when a slot becomes available.
     * (Called after a booking cancellation or capacity increase)
     *
     * @param int $scheduleId
     * @return void
     */
    public function processWaitlistForVacancy(int $scheduleId): void
    {
        $schedule = $this->courseRepository->getCourseScheduleById($scheduleId);

        if (!$schedule || $schedule->booked_count >= $schedule->course->max_capacity) {
            return; // No vacancy
        }

        $nextWaitlistEntry = $this->courseRepository->getNextWaitlistEntry($scheduleId);

        if ($nextWaitlistEntry) {
            DB::beginTransaction();
            try {
                $nextWaitlistEntry->update(['status' => 'offered']);
                // Dispatch notification to the member on the waitlist
                // e.g., Notification::send($nextWaitlistEntry->member, new CourseSlotAvailable($nextWaitlistEntry));

                Log::info("Waitlist offer sent to member ID: {$nextWaitlistEntry->member_id} for schedule ID: {$scheduleId}.");
                DB::commit();
            } catch (Throwable $e) {
                DB::rollBack();
                Log::error("Failed to process waitlist for schedule ID: {$scheduleId}. Error: {$e->getMessage()}");
            }
        }
    }

    /**
     * Get course schedules with trainer and course details.
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSchedulesWithDetails(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        return $this->courseRepository->getAllCourseSchedules($filters);
    }
}
