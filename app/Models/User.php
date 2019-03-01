<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Cashier\Subscription;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Cashier\Billable;

/**
 * Class User
 * @package App\Models
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable, Billable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'birthday',
        'accept_term'
    ];

    protected $dates = [
        'trial_ends_at',
        'subscription_ends_at',
        'deleted_at'
    ];

    protected $with = [
        'settings:user_id,profile_photo'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the settings record associated with the user.
     */
    public function settings()
    {
        return $this->hasOne(Settings::class);

    }

    /**
     * Get the courses for the user.
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    /**
     * date conversion to Y-m-d format
     *
     * @param $value
     */
    public function setBirthdayAttribute($value)
    {
        $this->attributes['birthday'] = Carbon::parse($value)->format('Y-m-d');
    }

    /**
     * accept term conversion to integer format
     *
     * @param $value
     */
    public function setAcceptTermAttribute($value)
    {
        $this->attributes['accept_term'] = $value ? 1 : 0;
    }

    /**
     * password conversion to bcrypt
     *
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * Get the verification for the user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function verifyUser()
    {
        return $this->hasOne(VerifyUser::class, 'user_id');
    }

    /**
     * return user profile photo
     *
     * @param $user
     * @return mixed
     */
    public function profile_photo($user)
    {
        return $user->settings->profile_photo;
    }
}
