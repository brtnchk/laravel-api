<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\CourseResource;
use App\Http\Requests\Course\CreateRequest;
use App\Http\Requests\Course\UpdateRequest;


class CoursesController extends Controller
{
    const PAGINATE = 20;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Get(
     *   path="/api/courses ",
     *   summary="Get all courses",
     *   operationId="api.courses.index",
     *   tags={"Courses"},
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
        $course = Course::with(['category','user'])
            ->where('user_id', auth()->id())
            ->get();

        return CourseResource::collection($course);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Get(
     *   path="/api/my-courses",
     *   summary="Get user courses",
     *   operationId="api.authCourses",
     *   tags={"Courses"},
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
    public function authCourses()
    {
        return response()->json(['courses' => auth()->user()->courses]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateRequest  $request
     * @return \Illuminate\Http\Response
     *
     * @SWG\Post(
     *   path="/api/courses ",
     *   summary="Create course",
     *   operationId="api.courses.store",
     *   tags={"Courses"},
     *   @SWG\Parameter(
     *     name="title",
     *     in="formData",
     *     description="Title of course",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="description",
     *     in="formData",
     *     description="Description of course",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="category_id",
     *     in="formData",
     *     description="Category of course",
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
        auth()->user()->courses()->create($request->params());

        return response()->json([
            'message' => 'Course created successfully!',
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  Course $course
     * @return \Illuminate\Http\Response
     *
     * @SWG\Get(
     *   path="/api/courses/{id}",
     *   summary="Show the course",
     *   operationId="api.courses.show",
     *   tags={"Courses"},
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of course",
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
    public function show(Course $course)
    {
        return response()->json(['course' => $course]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  int  Course $course
     * @return \Illuminate\Http\Response
     *
     * @SWG\Put(
     *   path="/api/courses/{id}",
     *   summary="Update the course",
     *   operationId="api.courses.update",
     *   tags={"Courses"},
     *   consumes={"application/x-www-form-urlencoded"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of course",
     *     required=true,
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="title",
     *     in="formData",
     *     description="Title of course",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="description",
     *     in="formData",
     *     description="Description of course",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="category_id",
     *     in="formData",
     *     description="Category of course",
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
    public function update(UpdateRequest $request,Course $course)
    {
        $course->update($request->params());
        return response()->json([
            'course' => $course,
            'message' => 'Course updated successfully!',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Course $course
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     *
     * @SWG\Delete(
     *   path="/api/courses/{id}",
     *   summary="Delete the course",
     *   operationId="api.courses.destroy",
     *   tags={"Courses"},
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of course",
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
    public function destroy(Course $course)
    {
        $course->delete();

        return response()->json([
            'message' => 'Course deleted successfully!',
        ], 200);
    }

    /**
     * Search a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *
     * @SWG\Get(
     *   path="/api/search/course",
     *   summary="Get searched course(s) ",
     *   operationId="api.search.course",
     *   tags={"Courses"},
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
        $courses = Course::with(['user'])->where('public',true);

        if($request->q){
            $courses->where('title', 'like', "$request->q%");
        }

        if($request->category_id){
            $courses->where('category_id', $request->category_id);
        }

        if($request->date_from){
            $date_from = Carbon::createFromTimestamp($request->date_from);
            $courses->where('created_at', '>=', $date_from);
        }

        if($request->date_to){
            $date_to = Carbon::createFromTimestamp($request->date_to);
            $courses->where('created_at', '<=', $date_to);
        }

        if($request->page){
            $courses->skip($request->page * self::PAGINATE);
        }

        $courses->paginate(self::PAGINATE);
        $courses->orderBy('created_at', 'DESC');

        return CourseResource::collection( $courses->get() );
    }
}
