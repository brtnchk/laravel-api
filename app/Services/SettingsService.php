<?php

namespace App\Services;

use App\Models\Settings;
use App\Models\User;

class SettingsService
{
    /**
     * save user settings data
     *
     * @param $request
     * @return mixed
     */
    public static function save($request)
    {
        $request->merge(['user_id' => auth()->id()]);
        $input = $request->except('username');

        $settings = Settings::updateOrCreate([
            'user_id' => auth()->id()
        ],$input);

        User::where('id', auth()->id())->update([
            'username' => $request->username
        ]);

        return $settings;
    }
}
