<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'courses';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category_id'
    ];


    /**
     * Get the course that owns the user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Get the course that owns the category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
