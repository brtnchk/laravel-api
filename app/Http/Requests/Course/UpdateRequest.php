<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'title' => 'string|max:255',
            'description' => 'string',
            'category_id' => 'numeric'
        ];
    }

    /**
     * Get params which should be stored to database.
     *
     * @return array
     */
    public function params()
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'user_id' => auth()->id(),
        ];
    }
}
