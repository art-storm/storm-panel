<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuItemRequest extends FormRequest
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
        $array_target = config('menu.menu_target');

        return [
            'title' => 'required|max:100',
            'url' => 'sometimes|max:255',
            'icon_class' => 'sometimes|max:255',
            'color' => 'sometimes|max:7',
            'target' => [
                'required',
                Rule::in(array_keys($array_target)),
            ],
        ];
    }
}
