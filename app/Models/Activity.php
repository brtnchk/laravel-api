<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'recent_activity';

    protected $fillable = [
        'event',
        'user_id',
        'note_id',
        'course_id'
    ];

    /**
     * Get parent Note
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function note()
    {
        return $this->belongsTo(Note::class);
    }

    /**
     * Get parent Course
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
