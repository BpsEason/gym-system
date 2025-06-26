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
        Schema::create("trainers", function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained("users")->onDelete("cascade"); # Links to users table
            $table->string("specialty")->nullable();
            $table->text("bio")->nullable();
            $table->decimal("hourly_rate", 8, 2)->default(0.00);
            $table->string("employment_status")->default("active"); # e.g., active, leave, terminated
            $table->timestamp("last_salary_calculated_at")->nullable(); # To track monthly salary calculations
            $table->timestamps();
            $table->unique(["user_id"]); # Each user can only be one trainer
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("trainers");
    }
};
