<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreComponentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    /**
     * Получает правила валидации, применимые к запросу.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'standard_key' => 'required|string|in:gost34,gost19,ieee830,iso29148',
            'key' => [
                'nullable',
                'string',
                Rule::unique('components')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                }),
            ],
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:1',
        ];
    }
}
