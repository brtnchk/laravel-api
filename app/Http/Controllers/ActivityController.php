<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  Note $note
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *
     * @SWG\Get(
     *   path="/api/recent",
     *   summary="Show the recent activity",
     *   operationId="api.recent.show",
     *   tags={"Activity"},
     *   @SWG\Parameter(
     *     name="note_id",
     *     in="query",
     *     description="Id of note",
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="course_id",
     *     in="query",
     *     description="Id of course",
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
        $ids = [];
        $two_weeks_ago_date = Carbon::now()->subDays(14)->timestamp;

        $activity_ids = DB::table('recent_activity')
            ->select( DB::raw('max(id) as id') )
            ->where('user_id', auth()->id())
            ->where('created_at', '>', $two_weeks_ago_date)
            ->groupBy('note_id', 'course_id', 'event');

        if($request->note_id)
            $activity_ids->where('note_id', $request->note_id);

        if($request->course_id)
            $activity_ids->where('note_id', $request->course_id);

        $activity_ids = $activity_ids->get();

        foreach ($activity_ids as $id)
            $ids[] = $id->id;

        $activities = Activity::whereIn('id', $ids)
            ->with([
                'course' => function($query) {
                    $query->select('id', 'title');
                },
                'note' => function($query) {
                    $query->select('id', 'title', 'description', 'cover_image', 'type');
                }
            ])->get();

        return response()->json($activities, 200);
    }

    /**
     * Create activity
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/api/activity/create",
     *   summary="Create the activity",
     *   operationId="api.activity.create",
     *   tags={"Activity"},
     *   @SWG\Parameter(
     *     name="note_id",
     *     in="formData",
     *     description="Id of note",
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="course_id",
     *     in="formData",
     *     description="Id of course",
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
    public function create(Request $request)
    {
        if($request->note_id)
            $event = 'note_view';
        else if($request->course_id)
            $event = 'course_view';
        else
            return response()->json('Invalid data', 400);

        try {
            Activity::create([
                'user_id'   => auth()->id(),
                'event'     => $event,
                'note_id'   => $request->note_id,
                'course_id' => $request->course_id
            ]);
        }
        catch (\Exception $e)
        {
            return response()->json($e->getMessage(), 500);
        }

        return response()->json('Activity successfully created', 200);
    }
}
