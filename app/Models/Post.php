<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'text',
        'topic_id',
        'user_id',
        'reply_post_id'
    ];

    /**
     * User relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * Current user like
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function like()
    {
        return $this->hasOne(Like::class, 'post_id', 'id')
            ->where([
                'user_id' => auth()->id(),
                'vote' => 1
            ])
            ->select('vote', 'post_id');
    }

    /**
     * Current user dislike
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function dislike()
    {
        return $this->hasOne(Like::class, 'post_id', 'id')
            ->where([
                'user_id' => auth()->id(),
                'vote' => -1
            ])
            ->select('vote', 'post_id');
    }
}
