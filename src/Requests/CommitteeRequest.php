<?php

namespace Baytek\Laravel\Content\Types\Committee\Requests;

use Baytek\Laravel\Content\Models\Content;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CommitteeRequest extends FormRequest
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
            'title' => 'required|max:255|unique_in_type:committee',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.unique_in_type' => 'This committee already exists. Please choose a different title.',
        ];
    }
}
