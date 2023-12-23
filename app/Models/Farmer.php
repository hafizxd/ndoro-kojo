<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Kandang;
use App\Models\District;
use App\Models\Village;

class Farmer extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function province() 
    {
        return $this->belongsTo(Province::class);
    }

    public function regency() 
    {
        return $this->belongsTo(Regency::class);
    }

    public function district() 
    {
        return $this->belongsTo(District::class);
    }

    public function village() 
    {
        return $this->belongsTo(Village::class);
    }

    public function kandangs()
    {
        return $this->hasMany(Kandang::class, 'farmer_id');
    }
}
