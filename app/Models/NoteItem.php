<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoteItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'note_id',
        'position',
        'term_text',
        'term_image',
        'term_image2',
        'term_definition',
        'passage_text',
    ];


    /**
     * Get the note that owns the note_item.
     */
    public function note()
    {
        return $this->belongsTo(Note::class);
    }


    /**
     * clear term_text field
     *
     * @param $value
     * @return mixed
     */
    public function getTermTextAttribute($value)
    {
        $value = str_replace('>', '', $value);
        $value = str_replace('<', '', $value);

        return $value;
    }
}
