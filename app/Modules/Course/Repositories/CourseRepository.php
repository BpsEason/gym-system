<?php

namespace App\Modules\Course\Repositories;

use App\Modules\Course\Models\Course;
use App\Modules\Course\Models\CourseSchedule;
use App\Modules\Course\Models\CourseBooking;
use App\Modules\Course\Models\Waitlist;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class CourseRepository
{
    public function getAllCourses(): Collection
    {
        return Course::all();
    }

    public function getCourseById(int $id): ?Course
    {
        return Course::find($id);
    }

    public function createCourse(array $data): Course
    {
        return Course::create($data);
    }

    public function updateCourse(int $id, array $data): ?Course
    {
        $course = Course::find($id);
        if ($course) {
            $course->update($data);
        }
        return $course;
    }

    public function deleteCourse(int $id): bool
    {
        return Course::destroy($id);
    }

    public function getAllCourseSchedules(array $filters = []): Collection
    {
        $query = CourseSchedule::with(['course', 'trainer.user']);

        if (isset($filters['start_at']) && isset($filters['end_at'])) {
            $query->whereBetween('start_at', [$filters['start_at'], $filters['end_at']]);
        }
        if (isset($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }
        if (isset($filters['trainer_id'])) {
            $query->where('trainer_id', $filters['trainer_id']);
        }
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->get();
    }

    public function getCourseScheduleById(int $id): ?CourseSchedule
    {
        return CourseSchedule::with(['course', 'trainer.user'])->find($id);
    }

    public function createCourseSchedule(array $data): CourseSchedule
    {
        return CourseSchedule::create($data);
    }

    public function updateCourseSchedule(int $id, array $data): ?CourseSchedule
    {
        $schedule = CourseSchedule::find($id);
        if ($schedule) {
            $schedule->update($data);
        }
        return $schedule;
    }

    public function deleteCourseSchedule(int $id): bool
    {
        return CourseSchedule::destroy($id);
    }

    public function getBookingById(int $bookingId): ?CourseBooking
    {
        return CourseBooking::find($bookingId);
    }

    public function createBooking(array $data): CourseBooking
    {
        return CourseBooking::create($data);
    }

    public function updateBooking(int $id, array $data): ?CourseBooking
    {
        $booking = CourseBooking::find($id);
        if ($booking) {
            $booking->update($data);
        }
        return $booking;
    }

    public function deleteBooking(int $id): bool
    {
        return CourseBooking::destroy($id);
    }

    public function getWaitlistEntry(int $memberId, int $scheduleId): ?Waitlist
    {
        return Waitlist::where('member_id', $memberId)->where('course_schedule_id', $scheduleId)->first();
    }

    public function addMemberToWaitlist(int $memberId, int $scheduleId, int $position): Waitlist
    {
        return Waitlist::create([
            'member_id' => $memberId,
            'course_schedule_id' => $scheduleId,
            'position' => $position,
            'status' => 'active',
            'joined_at' => Carbon::now(),
        ]);
    }

    public function getNextWaitlistEntry(int $scheduleId): ?Waitlist
    {
        return Waitlist::where('course_schedule_id', $scheduleId)
                        ->where('status', 'active')
                        ->orderBy('position', 'asc')
                        ->first();
    }
}
