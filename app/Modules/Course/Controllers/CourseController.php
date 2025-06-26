<?php

namespace App\Modules\Course\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Course\Repositories\CourseRepository;
use App\Modules\Course\Services\CourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CourseController extends Controller
{
    protected $courseRepository;
    protected $courseService;

    public function __construct(CourseRepository $courseRepository, CourseService $courseService)
    {
        $this->courseRepository = $courseRepository;
        $this->courseService = $courseService;
    }

    /**
     * Display a listing of courses. (Public)
     */
    public function index(): JsonResponse
    {
        $courses = $this->courseRepository->getAllCourses();
        return response()->json($courses);
    }

    /**
     * Display the specified course. (Public)
     */
    public function show(int $id): JsonResponse
    {
        $course = $this->courseRepository->getCourseById($id);
        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }
        return response()->json($course);
    }

    /**
     * Create a new course. (Admin only)
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:group,private',
            'duration_minutes' => 'required|integer|min:1',
            'max_capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $this->authorize('create', Course::class); # Assumes CoursePolicy exists

        $course = $this->courseRepository->createCourse($request->all());
        return response()->json(['message' => 'Course created successfully.', 'course' => $course], 201);
    }

    /**
     * Update the specified course. (Admin only)
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|in:group,private',
            'duration_minutes' => 'sometimes|integer|min:1',
            'max_capacity' => 'sometimes|integer|min:1',
            'price' => 'sometimes|numeric|min:0',
        ]);

        $course = $this->courseRepository->getCourseById($id);
        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        $this->authorize('update', $course); # Assumes CoursePolicy exists

        $updatedCourse = $this->courseRepository->updateCourse($id, $request->all());
        return response()->json(['message' => 'Course updated successfully.', 'course' => $updatedCourse]);
    }

    /**
     * Delete the specified course. (Admin only)
     */
    public function destroy(int $id): JsonResponse
    {
        $course = $this->courseRepository->getCourseById($id);
        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        $this->authorize('delete', $course); # Assumes CoursePolicy exists

        $this->courseRepository->deleteCourse($id);
        return response()->json(['message' => 'Course deleted successfully.']);
    }

    /**
     * Get all course schedules. (Public, with filters)
     */
    public function getSchedules(Request $request): JsonResponse
    {
        $filters = $request->only(['start_at', 'end_at', 'course_id', 'trainer_id', 'status']);
        
        # Parse dates if provided
        if (isset($filters['start_at'])) {
            $filters['start_at'] = Carbon::parse($filters['start_at'])->startOfDay();
        }
        if (isset($filters['end_at'])) {
            $filters['end_at'] = Carbon::parse($filters['end_at'])->endOfDay();
        }

        $schedules = $this->courseService->getSchedulesWithDetails($filters);
        return response()->json($schedules);
    }

    /**
     * Get a specific course schedule. (Public)
     */
    public function getSchedule(int $id): JsonResponse
    {
        $schedule = $this->courseRepository->getCourseScheduleById($id);
        if (!$schedule) {
            return response()->json(['message' => 'Course schedule not found'], 404);
        }
        return response()->json($schedule);
    }

    /**
     * Create a new course schedule. (Admin only)
     */
    public function storeSchedule(Request $request): JsonResponse
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'trainer_id' => 'required|exists:trainers,id',
            'start_at' => 'required|date|after_or_equal:now',
            'end_at' => 'required|date|after:start_at',
            'max_capacity' => 'sometimes|integer|min:1', # Can override course default
        ]);

        $this->authorize('createSchedule', CourseSchedule::class); # Assumes CourseSchedulePolicy exists

        $data = $request->all();
        // Use course's max_capacity if not provided
        if (!isset($data['max_capacity'])) {
            $course = $this->courseRepository->getCourseById($data['course_id']);
            $data['max_capacity'] = $course->max_capacity ?? Config::get('course.default_max_capacity');
        }
        $data['booked_count'] = 0; // Initialize booked count

        $schedule = $this->courseRepository->createCourseSchedule($data);
        return response()->json(['message' => 'Course schedule created successfully.', 'schedule' => $schedule], 201);
    }

    /**
     * Update a course schedule. (Admin only)
     */
    public function updateSchedule(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'course_id' => 'sometimes|exists:courses,id',
            'trainer_id' => 'sometimes|exists:trainers,id',
            'start_at' => 'sometimes|date',
            'end_at' => 'sometimes|date|after:start_at',
            'booked_count' => 'sometimes|integer|min:0',
            'status' => 'sometimes|in:scheduled,cancelled,completed',
            'max_capacity' => 'sometimes|integer|min:1',
        ]);

        $schedule = $this->courseRepository->getCourseScheduleById($id);
        if (!$schedule) {
            return response()->json(['message' => 'Course schedule not found'], 404);
        }

        $this->authorize('updateSchedule', $schedule); # Assumes CourseSchedulePolicy exists

        $updatedSchedule = $this->courseRepository->updateCourseSchedule($id, $request->all());
        return response()->json(['message' => 'Course schedule updated successfully.', 'schedule' => $updatedSchedule]);
    }

    /**
     * Delete a course schedule. (Admin only)
     */
    public function destroySchedule(int $id): JsonResponse
    {
        $schedule = $this->courseRepository->getCourseScheduleById($id);
        if (!$schedule) {
            return response()->json(['message' => 'Course schedule not found'], 404);
        }

        $this->authorize('deleteSchedule', $schedule); # Assumes CourseSchedulePolicy exists

        $this->courseRepository->deleteCourseSchedule($id);
        return response()->json(['message' => 'Course schedule deleted successfully.']);
    }

    /**
     * Book a specific course schedule for the authenticated member. (Member action)
     */
    public function bookSchedule(int $scheduleId): JsonResponse
    {
        $memberId = Auth::id();
        $result = $this->courseService->bookCourse($memberId, $scheduleId);

        if ($result['success']) {
            return response()->json(['message' => $result['message'], 'booking' => $result['booking']], 201);
        } else {
            return response()->json(['message' => $result['message']], 400);
        }
    }

    /**
     * Cancel a specific course booking for the authenticated member. (Member action)
     */
    public function cancelBooking(int $bookingId): JsonResponse
    {
        $memberId = Auth::id();
        $result = $this->courseService->cancelBooking($bookingId, $memberId);

        if ($result['success']) {
            return response()->json(['message' => $result['message']]);
        } else {
            return response()->json(['message' => $result['message']], 400);
        }
    }

    /**
     * Add the authenticated member to a waitlist. (Member action)
     */
    public function joinWaitlist(int $scheduleId): JsonResponse
    {
        $memberId = Auth::id();
        $result = $this->courseService->addToWaitlist($memberId, $scheduleId);

        if ($result['success']) {
            return response()->json(['message' => $result['message'], 'waitlist_entry' => $result['waitlist_entry']], 201);
        } else {
            return response()->json(['message' => $result['message']], 400);
        }
    }
}
