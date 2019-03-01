<?php

namespace App\Services;

use Carbon\Carbon;

class SubscriptionService
{
    static $customer_plan_id;
    static $customer_plan_name;
    static $admin_plan_id;
    static $admin_plan_name;
    static $trial;

    /**
     * custom __construct method
     */
    public static function init()
    {
        self::$customer_plan_id   = config('stripe.' . env('APP_ENV') . '.customer_plan.id');
        self::$customer_plan_name = config('stripe.' . env('APP_ENV') . '.customer_plan.name');
        self::$admin_plan_id      = config('stripe.' . env('APP_ENV') . '.admin_plan.id');
        self::$admin_plan_name    = config('stripe.' . env('APP_ENV') . '.admin_plan.name');
        self::$trial              = config('stripe.' . env('APP_ENV') . '.customer_plan.trial');
    }

    /**
     * create subscription
     *
     * @param $user
     * @param $token
     */
    public static function create($user, $token)
    {
        self::init();

        //subscription for admins
        if( stristr($user->email, '@memscore.com') || self::isBeforeMarch()) {

            $user->newSubscription(self::$admin_plan_name, self::$admin_plan_id)
                ->create($token, [
                    'email' => $user->email
                ]);

        //subscription for customers
        } else {

            $user->newSubscription(self::$customer_plan_name, self::$customer_plan_id)
                ->trialDays(self::$trial)
                ->create($token, [
                    'email'   => $user->email,
                ]);

        }
    }

    public static function isBeforeMarch()
    {
        return ( Carbon::now() <= Carbon::create('2019','3','31') );
    }

    /**
     * create subscription if not exists
     *
     * @param $user - user object
     */
    public static function createIfNotExist($user, $token)
    {
        self::init();

        if( !$user->subscription(self::$customer_plan_name) || !$user->subscription(self::$admin_plan_name) )
            self::create($user, $token);
    }
}
