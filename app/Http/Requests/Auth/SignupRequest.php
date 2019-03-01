<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SignupRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'username'    => 'required|alpha_num|min:3|unique:users',
            'email'       => 'required|email|unique:users',
            'password'    => 'required|string|min:5',
            'day'         => 'required|integer|min:1|max:31',
            'month'       => 'required|integer|min:1|max:12',
            'year'        => 'required|integer',
            'accept_term' => 'required',
            'token'       => 'required|string'
        ];
    }
}
