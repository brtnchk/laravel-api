<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 17.10.18
 * Time: 18:48
 */

namespace App\Data;


abstract class Account
{
    const TYPES = [
        'student' => 'Student',
        'teacher' => 'Teacher',
        'professional' => 'Professional',
        'other' => 'Other'
    ];

    const APPEAR = [
        'show_online_indicator' => "Show indicator when your're online",
        'show_real_name' => 'Show your real name on Menrva',
        'show_profile_in_google' => 'Show my profile in Google search results'
    ];
}