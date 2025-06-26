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
        Schema::create("member_tiers", function (Blueprint $table) {
            $table->id();
            $table->foreignId("member_id")->constrained("users")->onDelete("cascade"); # Links to users table
            $table->string("tier_name");
            $table->integer("points")->default(0);
            $table->timestamp("expires_at")->nullable();
            $table->timestamps();
            $table->unique(["member_id"]); # Each member has only one tier entry
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("member_tiers");
    }
};
