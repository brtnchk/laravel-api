<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoteStat extends Model
{
    protected $fillable = [
        'note_id',
        'user_id',
        'session_length',
        'words_total',
        'words_memorized',
        'words_memorized_avg',
        'words_memorized_max',
        'memorization_min',
        'memorization_max'
    ];

    public function note()
    {
        return $this->belongsTo(Note::class);
    }
}
