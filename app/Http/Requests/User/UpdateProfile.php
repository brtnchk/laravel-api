<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfile extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @param $user
     * @return array
     */
    public function rules()
    {
        return [
            'username'    => 'required|alpha_num|min:3|unique:users,username,'. auth()->id(),
            'email'       => 'required|email|unique:users,email,'. auth()->id(),
            'day'         => 'required|integer|min:1|max:31',
            'month'       => 'required|integer|min:1|max:12',
            'year'        => 'required|integer'
        ];
    }
}
