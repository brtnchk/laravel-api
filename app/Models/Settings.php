<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class Settings extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'profile_photo',
        'account_type',
        'appear_type'
    ];

    /**
     * Get the settings that owns the user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAccountTypeAttribute($value)
    {
        return $value ? explode(';', $value) : [];
    }

    public function getAppearTypeAttribute($value)
    {
        return $value ? explode(';', $value) : [];
    }
}
