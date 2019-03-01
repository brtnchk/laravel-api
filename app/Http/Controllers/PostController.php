<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\CreateRequest;
use App\Models\Post;
use App\Services\VoteService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param CreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/api/forum/post",
     *   summary="Create post",
     *   operationId="api.post.store",
     *   tags={"Forum"},
     *   @SWG\Parameter(
     *     name="topic_id",
     *     in="formData",
     *     description="Topic id",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="reply_post_id",
     *     in="formData",
     *     description="Reply post id",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="text",
     *     in="formData",
     *     description="Text of post",
     *     required=true,
     *     type="string"
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

        try {
            $post = Post::create( $request->all() );
        }
        catch (\Exception $e) {
            return response()->json('Incorrect topic_id', 404);
        }

        return response()->json($post, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/api/forum/post/{post_id}/like",
     *   summary="Post like",
     *   operationId="api.like.create",
     *   tags={"Forum"},
     *   @SWG\Parameter(
     *     name="post_id",
     *     in="formData",
     *     description="post_id",
     *     required=true,
     *     type="string"
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
    public function like(Request $request)
    {
        VoteService::makeVote($request->post_id, 1);
        $response = VoteService::getLikesCounter($request->post_id);

        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/api/forum/post/{post_id}/dislike",
     *   summary="Post dislike",
     *   operationId="api.dislike.create",
     *   tags={"Forum"},
     *   @SWG\Parameter(
     *     name="post_id",
     *     in="formData",
     *     description="post_id",
     *     required=true,
     *     type="string"
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
    public function dislike(Request $request)
    {
        VoteService::makeVote($request->post_id, -1);
        $response = VoteService::getLikesCounter($request->post_id);

        return response()->json($response, 200);
    }
}
