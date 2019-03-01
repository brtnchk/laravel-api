<?php

namespace App\Http\Controllers;

use App\Data\Account;
use App\Mail\ResetPassword;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use App\Http\Requests\settings\PasswordRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class SettingsController extends Controller
{
     /**
     * @SWG\Get(
     *   path="/api/settings/data",
     *   summary="Settings",
     *   operationId="getSettingData",
     *   tags={"Settings"},
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
    public function getSettingData()
    {
        return response()->json([
            'account_types' => Account::TYPES,
            'account_appears' => Account::APPEAR
        ]);
    }



    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/api/settings/update",
     *   summary="Update settings",
     *   operationId="updateAuthSettings",
     *   tags={"Settings"},
     *   @SWG\Parameter(
     *     name="account_type",
     *     in="formData",
     *     description="Change the type of account",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="appear_type",
     *     in="formData",
     *     description="Change the appears of account",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="username",
     *     in="formData",
     *     description="Change the username",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="profile_photo",
     *     in="formData",
     *     description="Update the profile photo",
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
     *
     */
    public function updateAuthSettings(Request $request)
    {
        $settings = SettingsService::save($request);
        return response()->json(['settings' => $settings], 201);
    }



    /**
     * Change the given user's password.
     *
     * @param PasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/api/settings/change-password",
     *   summary="Change password",
     *   operationId="changePassword",
     *   tags={"Settings"},
     *   @SWG\Parameter(
     *     name="old_password",
     *     in="formData",
     *     description="Old account password",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="password",
     *     in="formData",
     *     description="Account password",
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
     *
     */
    public function changePassword(PasswordRequest $request)
    {
        $user = auth()->user();

        if (!Hash::check($request->old_password, $user->password)) {
            abort(422,'Current password does not match.');
        }

        $user->password = $request->password;
        $user->save();

        $data = [
            'message' => 'Password successfully reset.'
        ];

        Mail::to($user->email)->send(new ResetPassword($user, $request->password));

        return response()->json($data);
    }
}
