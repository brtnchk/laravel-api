<?php

namespace App\Http\Requests\NoteItem;

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
            'position'        => 'integer|nullable',
            'term_text'       => 'string|nullable',
            'term_image'      => 'string|nullable',
            'term_image2'     => 'string|nullable',
            'term_definition' => 'string|nullable',
            'passage_text'    => 'string|nullable'
        ];
    }
}
