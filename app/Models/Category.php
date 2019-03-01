<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug'
    ];


    /**
     * Get the course record associated with the category.
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
