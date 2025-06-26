<?php

namespace App\Models;

use App\Models\User;
use App\Modules\Membership\Models\MemberTier;
use App\Modules\Course\Models\CourseBooking;

/**
 * Class Member
 * This model serves as an alias or extension for the User model specifically for
 * scenarios where the 'User' is acting as a 'Gym Member'.
 * Foreign keys like 'member_id' will typically reference 'users.id'.
 */
class Member extends User
{
    # No need to define $table as it inherits from User (uses 'users' table)
    # No need to define $fillable, $hidden, $casts as it inherits from User

    # Define relationships specific to a member role
    public function memberTier()
    {
        return $this->hasOne(MemberTier::class, 'member_id');
    }

    public function courseBookings()
    {
        return $this->hasMany(CourseBooking::class, 'member_id');
    }
}
