<?php

namespace App\Http\Requests;

use App\Models\School;
use Gate;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

class StoreSchoolRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'email' => [
                'email:rfc',
                'nullable',
                'unique:users,email',
            ],
            'password' => [
                'string',
                'required',
            ],
            'contact' => [
                'string',
                'required',
                'unique:users,contact',
            ],
            'address' => [
                'string',
                'nullable',
            ],
        ];
    }

}
