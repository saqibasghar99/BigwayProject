<?php

namespace App\Http\Requests;

use App\Models\Guardian;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreGuardianRequest extends FormRequest
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
            'contact' => [
                'string',
                'required',
                'unique:users,contact'
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
            'address' => [
                'string',
                'nullable',
            ],
            'gender' => [
                'nullable',
                'in:' . implode(',', array_keys(Guardian::GENDER_RADIO)),
            ],
        ];
    }
}
