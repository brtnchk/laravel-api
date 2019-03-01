<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\Category\CreateRequest;
use App\Http\Requests\Category\UpdateRequest;
use App\Models\Category;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Get(
     *   path="/api/categories ",
     *   summary="Get all categories",
     *   operationId="api.categories.index",
     *   tags={"Category"},
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
        $category = Category::with('courses')->get();
        return CategoryResource::collection($category);    
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateRequest  $request
     * @return \Illuminate\Http\Response
     *
     * @SWG\Post(
     *   path="/api/categories ",
     *   summary="Create category",
     *   operationId="api.categories.store",
     *   tags={"Category"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="title",
     *     in="formData",
     *     description="Title of category",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="slug",
     *     in="formData",
     *     description="Slug of category",
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
        $category = Category::create($request->params());
        return new CategoryResource($category);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  Category $category
     * @return \Illuminate\Http\Response
     *
     * @SWG\Get(
     *   path="/api/categories/{id}",
     *   summary="Show the category",
     *   operationId="api.categories.show",
     *   tags={"Category"},
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of category",
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
     *   @SWG\Response(
     *       response=200,
     *       description="successful operation"
     *   ),
     *   @SWG\Response(response=400, description="Bad request"),
     *   @SWG\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Category $category
     * @return \Illuminate\Http\Response
     *
     * @SWG\Put(
     *   path="/api/categories/{id}",
     *   summary="Update the category",
     *   operationId="api.categories.update",
     *   tags={"Category"},
     *   consumes={"application/x-www-form-urlencoded"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of category",
     *     required=true,
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="title",
     *     in="formData",
     *     description="Title of category",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="slug",
     *     in="formData",
     *     description="Slug of category",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="An authorization header",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *       response=200,
     *       description="successful operation"
     *   ),
     *   @SWG\Response(response=400, description="Bad request"),
     *   @SWG\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function update(UpdateRequest $request, Category $category)
    {
        $category->update($request->all());
        return $category;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @return Category
     * @throws \Exception
     *
     * @SWG\Delete(
     *   path="/api/categories/{id}",
     *   summary="Delete the category",
     *   operationId="api.category.destroy",
     *   tags={"Category"},
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of category",
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
    public function destroy(Category $category)
    {
        if(!is_null($category))
            $category->delete();

        return $category;
    }
}
