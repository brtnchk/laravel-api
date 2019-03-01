<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $fillable = [
        'post_id',
        'category_id',
        'user_id'
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function post()
    {
        return $this->hasOne(Post::class, 'id', 'post_id')
            ->select('id', 'title', 'text', 'likes', 'user_id', 'created_at');
    }
}
