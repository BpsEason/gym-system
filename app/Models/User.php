<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Modules\Membership\Models\MemberTier;
use App\Modules\Course\Models\CourseBooking;
use App\Modules\Trainer\Models\Trainer;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    # Relationships for a User that is also a "Member"
    public function memberTier()
    {
        return $this->hasOne(MemberTier::class, 'member_id');
    }

    public function courseBookings()
    {
        return $this->hasMany(CourseBooking::class, 'member_id');
    }

    # If a user can also be a trainer (polymorphic or one-to-one)
    public function trainerProfile()
    {
        return $this->hasOne(Trainer::class, 'user_id');
    }
}
