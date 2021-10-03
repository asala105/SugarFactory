<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'gender',
        'interested_in',
        'dob',
        'height',
        'weight',
        'nationality',
        'net_worth',
        'currency',
        'bio'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function getFullNameAttribute()
    {
        return implode(' ', [$this->first_name, $this->last_name]);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function usertype(){
        return $this->hasOne(UserType::class);
    }

    public function userconnection()
    {
        return $this->hasMany(UserConnection::class);
    }
    public function userblocked()
    {
        return $this->hasMany(UserBlocked::class, 'from_user_id');
    }

    public function userfavorite()
    {
        return $this->hasMany(UserFavorite::class);
    }
    public function userhobby()
    {
        return $this->hasMany(UserHobby::class);
    }
    public function userinterest()
    {
        return $this->hasMany(UserInterest::class);
    }
    public function usermessage()
    {
        return $this->hasMany(UserMessage::class);
    }
    public function usernotification()
    {
        return $this->hasMany(UserNotification::class);
    }
    public function userpicture()
    {
        return $this->hasMany(UserPicture::class);
    }
}
