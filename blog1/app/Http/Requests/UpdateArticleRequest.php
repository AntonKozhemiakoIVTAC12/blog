<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Controllers\Admin\ArticleAdminController;
use Illuminate\Validation\Rule;

class UpdateArticleRequest extends FormRequest
{
    private array $standards = [
        'gost34' => 'ГОСТ 34',
        'gost19' => 'ГОСТ 19',
        'ieee830' => 'IEEE STD 830-1998',
        'iso29148' => 'ISO/IEC/IEEE 29148-2011'
    ];

    protected function prepareForValidation()
    {
        $this->merge([
            'user_id' => auth()->id()
        ]);
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'gost_data_serialized' => 'required|json',
            'standard' => 'required|in:'.implode(',', array_keys($this->standards)),
            'user_id' => 'exists:users,id',
        ];
    }
}
