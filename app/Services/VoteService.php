<?php

namespace  App\Services;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

class VoteService
{
    static $vote;
    static $attributes;
    static $values;

    /**
     * @param $post_id
     * @param $vote
     */
    public static function init($post_id, $vote)
    {
        self::$attributes = [
            'user_id' => auth()->id(),
            'post_id' => $post_id,
        ];

        self::$values = [
            'user_id' => auth()->id(),
            'post_id' => $post_id,
            'vote'    => $vote
        ];
    }

    /**
     * Update likes counter
     *
     * @param $post_id
     * @param $vote
     * @return \Illuminate\Http\JsonResponse
     */
    public static function makeVote($post_id, $vote)
    {
        self::init($post_id, $vote);

        if( Post::find($post_id) )
        {
            DB::transaction(function(){
                $result = Like::updateOrCreate(self::$attributes, self::$values);

                if($result->wasRecentlyCreated)
                    Post::find(self::$values['post_id'])->increment('likes', self::$values['vote']);
                else if( $result->wasChanged() )
                    Post::find(self::$values['post_id'])->increment('likes', self::$values['vote'] * 2);
            });
        }
        else
        {
            return response()->json('Post not found.', 404);
        }
    }

    /**
     * return likes and dislikes counter by post_id
     *
     * @param $post_id
     * @return array
     */
    public static function getLikesCounter($post_id)
    {
        return [
            'likes'    => self::getLikes($post_id),
            'dislikes' => self::getDislikes($post_id)
        ];
    }

    /**
     * return likes counter by post_id
     *
     * @return \Illuminate\Support\Collection
     * @param $post_id
     */
    public static function getLikes($post_id)
    {
        $likes = DB::table('likes')
            ->select(DB::raw('count(id) as counter'))
            ->where([
                'vote'    => 1,
                'post_id' => $post_id
            ]);

        return $likes->value('counter');
    }

    /**
     * return dislikes counter by post_id
     *
     * @return \Illuminate\Support\Collection
     * @param $post_id
     */
    public static function getDislikes($post_id)
    {
        $dislikes = DB::table('likes')
            ->select(DB::raw('count(id) as counter'))
            ->where([
                'vote'    => -1,
                'post_id' => $post_id
            ]);

        return $dislikes->value('counter');
    }
}