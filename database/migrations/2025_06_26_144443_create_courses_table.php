<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("courses", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->text("description")->nullable();
            $table->string("type")->default("group"); # e.g., group, private
            $table->integer("duration_minutes")->default(60);
            $table->integer("max_capacity")->default(20);
            $table->decimal("price", 8, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create("course_schedules", function (Blueprint $table) {
            $table->id();
            $table->foreignId("course_id")->constrained("courses")->onDelete("cascade");
            $table->foreignId("trainer_id")->constrained("trainers")->onDelete("restrict"); # Prevent deleting trainer if schedules exist
            $table->dateTime("start_at");
            $table->dateTime("end_at");
            $table->integer("booked_count")->default(0);
            $table->string("status")->default("scheduled"); # e.g., scheduled, cancelled, completed
            $table->timestamps();
        });

        Schema::create("course_bookings", function (Blueprint $table) {
            $table->id();
            $table->foreignId("member_id")->constrained("users")->onDelete("cascade"); # Links to users table for members
            $table->foreignId("course_schedule_id")->constrained("course_schedules")->onDelete("cascade");
            $table->string("booking_status")->default("confirmed"); # e.g., confirmed, cancelled, completed, waitlisted
            $table->integer("points_earned")->default(0); # Points from this booking
            $table->timestamps();
            $table->unique(["member_id", "course_schedule_id"]); # Prevent duplicate bookings
        });

        Schema::create("waitlists", function (Blueprint $table) {
            $table->id();
            $table->foreignId("member_id")->constrained("users")->onDelete("cascade");
            $table->foreignId("course_schedule_id")->constrained("course_schedules")->onDelete("cascade");
            $table->timestamp("joined_at")->useCurrent();
            $table->integer("position")->nullable(); # Position in the waitlist
            $table->string("status")->default("active"); # active, offered, accepted, rejected, expired
            $table->timestamps();
            $table->unique(["member_id", "course_schedule_id"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("waitlists");
        Schema::dropIfExists("course_bookings");
        Schema::dropIfExists("course_schedules");
        Schema::dropIfExists("courses");
    }
};
