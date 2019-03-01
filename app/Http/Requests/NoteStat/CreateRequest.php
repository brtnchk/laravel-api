<?php

namespace App\Http\Requests\NoteStat;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
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
            'note_id'             => 'integer',
            'session_length'      => 'integer',
            'words_total'         => 'integer',
            'words_memorized'     => 'integer',
            'words_memorized_avg' => 'numeric',
            'words_memorized_max' => 'integer',
            'memorization_min'    => 'numeric|between:0,1',
            'memorization_max'    => 'numeric|between:0,1'
        ];
    }
}
