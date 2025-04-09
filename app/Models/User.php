<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// @author : Pakkapon Chomchoey 66160080
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $primaryKey = 'us_id';

    public $timestamps = true;
    protected $fillable = [
        'us_fname',
        'us_lname',
        'us_email',
        'us_role',
        'us_head',
    ];

    protected $casts = [
        'us_role' => 'string',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function head()
    {
        return $this->belongsTo(User::class, 'us_head', 'us_id')
            ->where('us_role', 'Sales Supervisor');
    }
    public function salesTeam()
    {
        return $this->hasMany(User::class, 'us_head', 'us_id')
            ->where('us_role', 'Sales');
    }

    public function branch()
    {
        return $this->hasMany(Branch::class, 'br_us_id', 'us_id');
    }

    public function interestLocation()
    {
        return $this->hasMany(InterestLocation::class, 'il_us_id', 'us_id');
    }

    public function order()
    {
        return $this->hasMany(Order::class, 'od_us_id', 'us_id');
    }
}
