<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'position',
        'user_id',
        'course_id',
        'category_id',
        'type',
        'cover_image',
        'public'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function note_items()
    {
        return $this->hasMany(NoteItem::class);
    }

    public function note_stats()
    {
        return $this->hasMany(NoteStat::class);
    }

    /**
     * bool to int
     *
     * @param $value
     * @return int
     */
    public function setPublicAttribute($value)
    {
        if($value === 'true' || $value === true)
            $this->attributes['public'] = 1;
        else if($value === 'false' || $value === false)
            $this->attributes['public'] = 0;
    }

    /**
     * int to bool
     *
     * @param $value
     * @return bool
     */
    public function getPublicAttribute($value)
    {
        if($value === 1)
            return true;
        else if($value === 0)
            return false;
    }

    /**
     * check if current user author
     *
     * @return bool
     */
    public function is_author()
    {
        return auth()->id() == $this->user_id;
    }
}
