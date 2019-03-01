<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Password\CreateRequest;
use App\Http\Requests\Password\ResetRequest;
use App\Mail\ResetPasswordRequest;
use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    /**
     * Create reset password token
     *
     * @param CreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/api/password/reset",
     *   summary="Reset user password request",
     *   operationId="reset",
     *   tags={"Forgot password"},
     *   @SWG\Parameter(
     *       name="email",
     *       in="formData",
     *       description="Email",
     *       required=true,
     *       type="string"
     *   ),
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     */
    public function create(CreateRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user)
            return response()->json('We can\'t find a user with that e-mail address.', 404);

        $passwordReset = PasswordReset::updateOrCreate(['email' => $user->email], [
            'email' => $user->email,
            'token' => uniqid( sha1(time()) )
        ]);

        Mail::to($user->email)->send( new ResetPasswordRequest($user, $passwordReset->token) );
        return response()->json('We have e-mailed your password reset link!', 200);
    }


    /**
     * Reset user password by token
     *
     * @param ResetRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/api/password/change",
     *   summary="Reset user password request",
     *   operationId="change",
     *   tags={"Forgot password"},
     *   @SWG\Parameter(
     *       name="password",
     *       in="formData",
     *       description="Password",
     *       required=true,
     *       type="string"
     *   ),
     *   @SWG\Parameter(
     *       name="token",
     *       in="formData",
     *       description="Token",
     *       required=true,
     *       type="string"
     *   ),
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     */
    public function reset(ResetRequest $request)
    {
        $passwordReset = PasswordReset::where([
            'token' => $request->token,
        ])->first();

        if (!$passwordReset)
            return response()->json('This password reset token is invalid.', 404);

        $user = User::where('email', $passwordReset->email)->first();

        if (!$user)
            return response()->json('We can\'t find a user with that e-mail address.', 404);

        $user->password = $request->password;
        $user->save();
        $passwordReset->delete();

        return response()->json($user, 200);
    }
}