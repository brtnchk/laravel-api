<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdateProfile;
use App\Models\User;

class UserController extends Controller
{
	/**
    * Update user records
    *
    * @param UpdateProfile $request
    * @return \Illuminate\Contracts\Auth\Authenticatable|null
    *
    * @SWG\Post(
    *   path="/api/user/update",
    *   summary="Update user records ",
    *   operationId="api.user.update",
    *   tags={"User"},
    *   @SWG\Parameter(
    *       name="username",
    *       in="formData",
    *       description="Update the username",
    *       required=true,
    *       type="string"
    *   ),
    *   @SWG\Parameter(
    *       name="email",
    *       in="formData",
    *       description="Update the email",
    *       required=true,
    *       type="string"
    *   ),
    *   @SWG\Parameter(
    *       name="day",
    *       in="formData",
    *       description="Day of birthday",
    *       required=true,
    *       type="integer"
    *   ),
    *   @SWG\Parameter(
    *       name="month",
    *       in="formData",
    *       description="Month of birthday",
    *       required=true,
    *       type="integer"
    *   ),
    *   @SWG\Parameter(
    *       name="year",
    *       in="formData",
    *       description="Year of birthday",
    *       required=true,
    *       type="integer"
    *   ),
    *   @SWG\Parameter(
    *     name="Authorization",
    *     in="header",
    *     description="An authorization header",
    *     required=true,
    *     type="string"
    *   ),
    *   @SWG\Response(response=200, description="successful operation"),
    *   @SWG\Response(response=406, description="not acceptable"),
    *   @SWG\Response(response=500, description="internal server error")
    * )
    */
    public function update(UpdateProfile $request)
    {
        auth()->user()->update( $request->all() );
        return auth()->user();
    }

    /**
     * Deactivate user account
     *
     * @SWG\Post(
     *   path="/api/user/deactivate",
     *   summary="Deactivate user account",
     *   operationId="api.user.deactivate",
     *   tags={"User"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="An authorization header",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     */
    public function deactivate()
    {
        $user = auth()->user();
        $admin_subscription    = config('stripe.' . env('APP_ENV') . '.admin_plan.name');
        $customer_subscription = config('stripe.' . env('APP_ENV') . '.customer_plan.name');

        if( $user->subscription($customer_subscription) )
        {
            $user->subscription($customer_subscription)->cancelNow();
        }
        else if( $user->subscription($admin_subscription) )
        {
            $user->subscription($admin_subscription)->cancelNow();
        }

        return response()->json($user->delete(), 200);
    }
}
