<?php

namespace App\Http\Controllers\Auth;

use App\Mail\VerifyMail;
use App\Models\User;
use App\Models\VerifyUser;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SignupRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use PhpParser\Node\Expr\Cast\Object_;

class ApiAuthController extends Controller
{
    /**
    * Max limit needed authorization sessions.
    */
    private const LIMIT_OF_SESSIONS = 3;

    protected $user;

    protected $token;

    /**
    * Signup user
    *
    * @param SignupRequest $request
    * @return \Illuminate\Http\JsonResponse
    *
    * @SWG\Post(
    *   path="/api/register",
    *   summary="Registration user into the system",
    *   operationId="signup",
    *   tags={"Auth"},
    *   @SWG\Parameter(
    *       name="username",
    *       in="formData",
    *       description="Username",
    *       required=true,
    *       type="string"
    *   ),
    *   @SWG\Parameter(
    *       name="email",
    *       in="formData",
    *       description="Email",
    *       required=true,
    *       type="string"
    *   ),
    *   @SWG\Parameter(
    *       name="password",
    *       in="formData",
    *       description="Password",
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
    *       name="accept_term",
    *       in="formData",
    *       description="Agree accepted term",
    *       required=true,
    *       type="boolean"
    *   ),
    *   @SWG\Response(response=200, description="successful operation"),
    *   @SWG\Response(response=406, description="not acceptable"),
    *   @SWG\Response(response=500, description="internal server error")
    * )
    */
    public function signup(SignupRequest $request)
    {

        $birthday = Carbon::parse("$request->year-$request->month-$request->day")->format('Y-m-d');
        $request->merge(['birthday' => $birthday]);
        $clearPassword = $request->password;

        DB::transaction(function() use ($request) {
            $this->user = User::create($request->all());
            $this->user->settings()->create([]);
            $this->user->createAsStripeCustomer($request->token);
            $this->token = $this->user->createToken('Personal Access Token');
            SubscriptionService::create($this->user, $request->token);
        });

        $this->sendVerificationMail($this->user, $clearPassword);
        return $this->respondWithToken($this->token->accessToken, $this->user->load('settings'));
    }


    /**
     * send verification mail after signup
     *
     * @param $user
     * @param $clearPassword
     */
    public function sendVerificationMail($user, $clearPassword)
    {
        VerifyUser::create([
            'user_id' => $user->id,
            'token' => uniqid( sha1(time()) )
        ]);

        Mail::to($user->email)->send(new VerifyMail($user, $clearPassword));
    }


    /**
     * Login user and create token
     *
     * @param App\Http\Requests\Auth\SignupRequest $request
     * @return Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/api/login",
     *   summary="Login",
     *   operationId="login",
     *   tags={"Auth"},
     *   security={
     *     {"passport": {}},
     *   },
     *   @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     description="Email for login",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="username",
     *     in="formData",
     *     description="Username for login",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="password",
     *     in="formData",
     *     description="The password for login in clear text",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=400, description="internal server error")
     * )
     */
    public function login(LoginRequest $request)
    {
        $credentials = $this->credentials($request);

        if(!Auth::attempt($credentials))
        {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $this->checkLimitAuthSessions($request);

        $user = $request->user();
        $token = $user->createToken('Personal Access Token');

        return $this->respondWithToken($token->accessToken, $user->load('settings'));
    }


    /**
     * Logout user (Revoke the token)
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     * @SWG\Post(
     *   path="/api/logout",
     *   summary="Logout",
     *   operationId="logout",
     *   tags={"Auth"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="An authorization header",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response="default", description="successful operation"),
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }


    /**
     * Get the authenticated User
     *
     * @return Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/api/me",
     *   summary="User records",
     *   operationId="me",
     *   tags={"Auth"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="An authorization header",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response="default", description="successful operation"),
     *   ),
     * )
     */
    public function me()
    {
        return response()->json(auth()->user(), 201);
    }


    /**
     * Refresh a token.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/api/refresh",
     *   summary="Refresh access token",
     *   operationId="refreshAccessToken",
     *   tags={"Auth"},
     *   @SWG\Response(response="default", description="successful operation"),
     * )
     *
     */
    public function refreshAccessToken(Request $request)
    {
        $user = $request->user();
        $user->token()->revoke();
        $token = $user->createToken('Personal Access Token');

        return $this->respondWithToken($token->accessToken, $user->load('settings'));
    }


    /**
     *
     */
    public function addPayment()
    {
        //todo register payment
    }


    /**
     * Limit needed authorization sessions.
     *
     * @param Request $request
     */
    protected function checkLimitAuthSessions(Request $request)
    {
        $user = $request->user();
        $activeTokens = $user->tokens->where('revoked', 0)->count();

//        if ($activeTokens >= self::LIMIT_OF_SESSIONS) {
//
//            DB::table('oauth_access_tokens')
//                ->where( 'user_id', auth()->id() )
//                ->orderBy('created_at', 'asc')
//                ->take(1)
//                ->delete();
//        }
    }


    /**
     * Get the needed authorization credentials from the request.

     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->email ?
            $request->only('email', 'password') :
            $request->only('username', 'password');
    }


    /**
     * Get the token array structure.
     *
     * @param  string $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $user = null)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $user
        ], 200);
    }


    /**
     * user email confirmation
     *
     * @param $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirmEmail(Request $request)
    {
        $verifyUser = VerifyUser::where('token', $request->token)->first();

        if(isset($verifyUser))
        {
            $user = $verifyUser->user;

            if(!$user->verified)
            {
                $verifyUser->user->verified = 1;
                $verifyUser->user->save();
                $status = "Your e-mail is verified. You can now login.";
            }
            else
            {
                $status = "Your e-mail is already verified. You can now login.";
            }
        }
        else
        {
            return response()->json('Sorry your email cannot be identified',404);
        }

        if($request->redirect == 'true')
            return redirect( config('other.' . env('APP_ENV') . '.activation_redirect_url'), 301);

        return response()->json($status,200);
    }
}
