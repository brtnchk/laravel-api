<?php

namespace App\Http\Controllers;

use App\Http\Requests\Topic\CreateRequest;
use App\Http\Resources\TopicResource;
use App\Models\Post;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TopicController extends Controller
{
    const PAGINATE = 50;

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/api/forum/topic",
     *   summary="Create topic",
     *   operationId="api.topic.store",
     *   tags={"Forum"},
     *   @SWG\Parameter(
     *     name="title",
     *     in="formData",
     *     description="Title of topic",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="text",
     *     in="formData",
     *     description="Text of topic",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="category_id",
     *     in="formData",
     *     description="Id of category",
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="An authorization header",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function store(CreateRequest $request)
    {
        $request->merge(['user_id' => auth()->id()]);
        $topic = Topic::create( $request->all() );

        $request->merge(['topic_id' => $topic->id]);
        $post = Post::create( $request->all() );

        $topic->post_id = $post->id;
        $topic->save();

        return response()->json($topic, 200);
    }

    /**
     * Show topic with posts
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *
     * @SWG\Get(
     *   path="/api/forum/topic",
     *   summary="Show topics",
     *   operationId="api.topics.show",
     *   tags={"Forum"},
     *   @SWG\Parameter(
     *     name="sort",
     *     in="query",
     *     description="Sort",
     *     enum={"popular", "new", "followed"},
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     description="Page",
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="An authorization header",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function index(Request $request)
    {
        $topics = Topic::with(['post' => function($query){
            $query->with(['user:id,username,birthday', 'like', 'dislike']);
        }]);

        switch ($request->sort){
            case 'popular':
                $topics->select('topics.*',
                    DB::raw('(SELECT likes FROM posts WHERE topics.post_id = posts.id) as likes')
                )->orderBy('likes', 'DESC');
                break;
            case 'new':
                $topics->latest();
                break;
            case 'followed':
                break;
        }

        $topics = $topics->paginate(self::PAGINATE);
        return TopicResource::collection($topics);
    }

    /**
     * Show topic with posts
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *
     * @SWG\Get(
     *   path="/api/forum/topic/{topic_id}",
     *   summary="Show topic by id",
     *   operationId="api.topic.show",
     *   tags={"Forum"},
     *   @SWG\Parameter(
     *     name="topic_id",
     *     in="path",
     *     description="Topic id",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     description="Page",
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="An authorization header",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function show(Request $request)
    {
        $topic = Topic::where('id', $request->topic_id)->with([
            'post' => function($query){
                $query->with(['user:id,username,birthday', 'like', 'dislike']);
                $query->select('id', 'reply_post_id', 'topic_id', 'user_id', 'title', 'text', 'likes');
                $query->paginate(self::PAGINATE);
            },
            'posts' => function($query){
                $query->with(['user:id,username,birthday', 'like', 'dislike']);
                $query->select('id', 'reply_post_id', 'topic_id', 'user_id', 'title', 'text', 'likes', 'created_at');
                $query->paginate(self::PAGINATE);
            }
        ])->get();

        return TopicResource::collection($topic);
    }
}
