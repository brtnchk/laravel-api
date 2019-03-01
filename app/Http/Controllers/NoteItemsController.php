<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\NoteItem;
use App\Http\Resources\NoteItemsResource;
use App\Http\Requests\NoteItem\CreateRequest;
use App\Http\Requests\NoteItem\UpdateRequest;

class NoteItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $note_id
     * @return \Illuminate\Http\Response
     *
     * @SWG\Get(
     *   path="/api/notes/{note}/items",
     *   summary="Get all note's items",
     *   operationId="api.noteItems.index",
     *   tags={"Note's items"},
     *   @SWG\Parameter(
     *     name="note",
     *     in="path",
     *     description="Note Id",
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
    public function index(int $note_id)
    {
        $noteItems = NoteItem::where('note_id', $note_id)
            ->orderBy('position', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();

        return NoteItemsResource::collection($noteItems);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateRequest  $request
     * @param Note $note
     * @return \Illuminate\Http\Response
     *
     * @SWG\Post(
     *   path="/api/notes/{id}/items",
     *   summary="Create note's item",
     *   operationId="api.noteItems.store",
     *   tags={"Note's items"},
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of note",
     *     required=true,
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="position",
     *     in="formData",
     *     description="Position of note's item",
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="term_text",
     *     in="formData",
     *     description="Term of note's item",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="term_image",
     *     in="formData",
     *     description="Term image of note's item",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="term_definition",
     *     in="formData",
     *     description="Term definition of note's item",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="passage_text",
     *     in="formData",
     *     description="Passage text of note's item",
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
    public function store(CreateRequest $request, Note $note)
    {
        if( !$note->is_author() )
            return response()->json('Access denied');

        if(!$request->position && $request->position !== 0)
        {
            $request->merge([
                'position' => $note->note_items()->max('position') + 1
            ]);
        }

        $noteItem = $note->note_items()->create($request->all());
        return new NoteItemsResource($noteItem);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $note_id
     * @param int $note_item_id
     * @return \Illuminate\Http\Response
     *
     * @SWG\Get(
     *   path="/api/notes/{note}/items/{item}",
     *   summary="Show the note's item",
     *   operationId="api.noteItems.show",
     *   tags={"Note's items"},
     *   @SWG\Parameter(
     *     name="note",
     *     in="path",
     *     description="Note Id",
     *     required=true,
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="item",
     *     in="path",
     *     description="Id of note's item",
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
    public function show(int $note_id, int $note_item_id)
    {
        $noteItem = NoteItem::where('id', $note_item_id)
            ->where('note_id', $note_id)
            ->get();

        return new NoteItemsResource($noteItem);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  $note_item_id
     * @param  $note_id
     * @param  $request
     * @return \Illuminate\Http\Response
     *
     * @SWG\Put(
     *   path="/api/notes/{note}/items/{note_item}",
     *   summary="Update the noteItem",
     *   operationId="api.noteItems.update",
     *   tags={"Note's items"},
     *   @SWG\Parameter(
     *     name="note",
     *     in="path",
     *     description="Note id",
     *     required=true,
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="note_item",
     *     in="path",
     *     description="Id of note's item",
     *     required=true,
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="position",
     *     in="formData",
     *     description="Position of note's item",
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="term_text",
     *     in="formData",
     *     description="Term text of note's item",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="term_image",
     *     in="formData",
     *     description="Term image of note's item",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="term_definition",
     *     in="formData",
     *     description="Term definition of note's item",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="passage_text",
     *     in="formData",
     *     description="Passage text of note's item",
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
    public function update(UpdateRequest $request, Note $note, int $note_item_id)
    {
        if( !$note->is_author() )
            return response()->json('Access denied');

        $response = $note->note_items()
            ->where('id', $note_item_id)
            ->update( $request->all() );

        return $response;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $note_id
     * @param int $note_item_id
     * @return \Illuminate\Http\Response
     *
     * @SWG\Delete(
     *   path="/api/notes/{note}/items/{note_item} ",
     *   summary="Delete the note's item",
     *   operationId="api.noteItems.destroy",
     *   tags={"Note's items"},
     *   @SWG\Parameter(
     *     name="note",
     *     in="path",
     *     description="Id of note's item",
     *     required=true,
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="note_item",
     *     in="path",
     *     description="Id of note's item",
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
    public function destroy(int $note_id, int $note_item_id)
    {
        $response = NoteItem::where('id', $note_item_id)
            ->where('note_id', $note_id)
            ->delete();

        return $response;
    }
}
