<?php

namespace App\Modules\Membership\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class MemberTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'tier_name',
        'points',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }
}
