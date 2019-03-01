<?php

namespace App\Http\Middleware;

use Closure;

class CheckEmailConfirmation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if( $user && $user->verified )
            return $next($request);

        return response()->json([
            'message' => 'Your account must be confirmed before you can log in. Please check your e-mail.'
        ], 401);
    }
}
