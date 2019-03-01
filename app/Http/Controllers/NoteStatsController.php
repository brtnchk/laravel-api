<?php

namespace App\Http\Controllers;

use App\Http\Requests\NoteStat\CreateRequest;
use App\Http\Resources\NoteStatResource;
use App\Models\Note;
use App\Models\NoteStat;
use Illuminate\Http\Request;

class NoteStatsController extends Controller
{
    const PAGINATE = 20;

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  int  Note $note_id
     * @return \Illuminate\Http\Response
     *
     * @SWG\Get(
     *   path="/api/notes/{id}/stats",
     *   summary="Show the note stat",
     *   operationId="api.noteStat.show",
     *   tags={"Note's stats"},
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of note",
     *     required=true,
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     description="Page",
     *     required=false,
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
    public function index(Request $request, int $note_id)
    {
        $note_stats = NoteStat::whereHas('note',
            function($query) {
                $query->where('user_id', auth()->id());
                $query->orWhere('public', true);
            })->where([
                'note_id' => $note_id,
                'user_id' => auth()->id()
            ]);

        if($request->page)
            $note_stats = $note_stats->skip($request->page * self::PAGINATE);

        $note_stats->latest()->paginate(self::PAGINATE);

        return new NoteStatResource( $note_stats->get() );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateRequest $request
     * @return NoteStatResource
     *
     * @SWG\Post(
     *   path="/api/notes/{note_id}/stats",
     *   summary="Create note's stat",
     *   operationId="api.noteStat.store",
     *   tags={"Note's stats"},
     *   @SWG\Parameter(
     *     name="note_id",
     *     in="path",
     *     description="Id of note",
     *     required=true,
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="session_length",
     *     in="formData",
     *     description="Session length",
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="words_total",
     *     in="formData",
     *     description="Words total",
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="words_memorized",
     *     in="formData",
     *     description="Words memorized",
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="words_memorized_avg",
     *     in="formData",
     *     description="Words memorized avg",
     *     type="number"
     *   ),
     *   @SWG\Parameter(
     *     name="words_memorized_max",
     *     in="formData",
     *     description="Words memorized max",
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="memorization_min",
     *     in="formData",
     *     description="Memorization min",
     *     type="number"
     *   ),
     *   @SWG\Parameter(
     *     name="memorization_max",
     *     in="formData",
     *     description="Memorization max",
     *     type="number"
     *   ),
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="An authorization header",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response="default", description="successful operation"),
     *   @SWG\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function store(CreateRequest $request, int $note_id)
    {
        $note = Note::where([
            'id'     => $note_id,
            'public' => true
        ])
            ->orWhere('user_id', auth()->id())
            ->where('id', $note_id)
            ->first();

        if($note)
        {
            $request->merge([
                'user_id' => auth()->id(),
                'note_id' => $note_id
            ]);

            $stat = NoteStat::create( $request->all() );
            return new NoteStatResource($stat);
        }

        return response()->json(['message' => 'Not found'], 404);
    }
}
