<?php

namespace App\Http\Middleware;

use Closure;

class CheckSubscription
{
    protected $user;
    protected $admin_plan;
    protected $customer_plan;


    public function __construct()
    {
        $this->user = auth()->user();
        $this->admin_plan    = config('stripe.' . env('APP_ENV') . '.admin_plan.name');
        $this->customer_plan = config('stripe.' . env('APP_ENV') . '.customer_plan.name');
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if(
            $this->isCurrentUserAdmin() ||
            ( $this->user->subscription($this->admin_plan) &&
              $this->user->subscription($this->customer_plan)->onTrial() ) ||
            $this->user->subscribed($this->customer_plan)
        ) return $next($request);

        return response()->json('No subscription',402);
    }

    /**
     * check is user admin
     *
     * @return bool
     */
    public function isCurrentUserAdmin()
    {
        return $this->user->subscription($this->admin_plan);
    }
}
