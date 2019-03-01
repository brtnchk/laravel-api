<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\NoteResource;
use App\Http\Requests\Note\CreateRequest;
use App\Http\Requests\Note\UpdateRequest;
use App\Models\Note;
use Illuminate\Support\Carbon;

class NotesController extends Controller
{
    const PAGINATE = 20;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Get(
     *   path="/api/notes ",
     *   summary="Get all notes",
     *   operationId="api.notes.index",
     *   tags={"Notes"},
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
    public function index()
    {
        $notes = Note::with(['course'])
            ->where('user_id', auth()->id())
            ->orderBy('position', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();

        return NoteResource::collection($notes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateRequest  $request
     * @return \Illuminate\Http\Response
     *
     * @SWG\Post(
     *   path="/api/notes",
     *   summary="Create note",
     *   operationId="api.notes.store",
     *   tags={"Notes"},
     *   @SWG\Parameter(
     *     name="title",
     *     in="formData",
     *     description="Title of note",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="description",
     *     in="formData",
     *     description="Description of note",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="position",
     *     in="formData",
     *     description="Position of note",
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="course_id",
     *     in="formData",
     *     description="Id of course",
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="type",
     *     in="formData",
     *     description="Type of note",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="cover_image",
     *     in="formData",
     *     description="Cover image of note",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="public",
     *     in="formData",
     *     description="Visibility of note",
     *     type="boolean"
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
        $position = Note::where('user_id', auth()->id())
                ->max('position') + 1;

        $request->merge([
            'user_id'  => auth()->id(),
            'position' => $position
        ]);

        $note = Note::create( $request->all() );

        return new NoteResource($note);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  Note $note
     * @return \Illuminate\Http\Response
     *
     * @SWG\Get(
     *   path="/api/notes/{id}",
     *   summary="Show the note",
     *   operationId="api.notes.show",
     *   tags={"Notes"},
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of note",
     *     required=true,
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
    public function show(Note $note)
    {
        return new NoteResource($note);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Note $note
     * @return \Illuminate\Http\Response
     *
     * @SWG\Put(
     *   path="/api/notes/{id} ",
     *   summary="Update the note",
     *   operationId="api.notes.update",
     *   tags={"Notes"},
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of note",
     *     required=true,
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="title",
     *     in="formData",
     *     description="Title of note",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="description",
     *     in="formData",
     *     description="Description of note",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="position",
     *     in="formData",
     *     description="Position of note",
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="course_id",
     *     in="formData",
     *     description="Id of course",
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="category_id",
     *     in="formData",
     *     description="Id of category",
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="type",
     *     in="formData",
     *     description="Type of note",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="cover_image",
     *     in="formData",
     *     description="Cover image of note",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="public",
     *     in="formData",
     *     description="Visibility of note",
     *     type="boolean"
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
    public function update(UpdateRequest $request, Note $note)
    {
        if ( !$note->is_author() )
            return response()->json('Access denied');

        $note->update( $request->all() );
        return new NoteResource($note);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Note $note
     * @return NoteResource
     * @throws \Exception
     *
     * @SWG\Delete(
     *   path="/api/notes/{id}",
     *   summary="Delete the note",
     *   operationId="api.note.destroy",
     *   tags={"Notes"},
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of note",
     *     required=true,
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
    public function destroy(Note $note)
    {
        if ( !$note->is_author() )
            return response()->json('Access denied');

        if($note)
            $note->delete();

        return new NoteResource($note);
    }

    /**
     * Search a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *
     * @SWG\Get(
     *   path="/api/search/note",
     *   summary="Get searched note(s) ",
     *   operationId="api.search.note",
     *   tags={"Notes"},
     *   @SWG\Parameter(
     *     name="q",
     *     in="query",
     *     description="Query",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="category_id",
     *     in="query",
     *     description="Category ID",
     *     required=false,
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="date_from",
     *     in="query",
     *     description="Date from (unix timestamp)",
     *     required=false,
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="date_to",
     *     in="query",
     *     description="Date to (unix timestamp)",
     *     required=false,
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="type",
     *     in="query",
     *     description="Type",
     *     required=false,
     *     type="string"
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
    public function search(Request $request)
    {
        $notes = Note::with(['user:id,username,birthday'])->where('public',true);

        if($request->q){
            $notes->where('title', 'like', "%$request->q%");
        }

        if($request->category_id){
            $notes->where('category_id', $request->category_id);
        }

        if($request->type){
           $notes->where('type', '=', $request->type);
        }

        if($request->date_from){
            $date_from = Carbon::createFromTimestamp($request->date_from);
            $notes->where('created_at', '>=', $date_from);
        }

        if($request->date_to){
            $date_to = Carbon::createFromTimestamp($request->date_to);
            $notes->where('created_at', '<=', $date_to);
        }

        if($request->page){
            $notes->skip($request->page * self::PAGINATE);
        }

        $notes->paginate(self::PAGINATE);
        $notes->orderBy('created_at', 'DESC');
        $notes = $notes->get();

        foreach ($notes as $key => $note)
        {
            $note['user']['profile_photo'] = $note['user']['settings']['profile_photo'];
            $note['user']['account_type'] = $note['user']['settings']['account_type'];
            unset($note['user']['settings']);
        }

        return new NoteResource($notes);
    }
}
