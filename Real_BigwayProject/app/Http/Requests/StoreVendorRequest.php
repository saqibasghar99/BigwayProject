<?php

namespace App\Http\Requests;

use App\Models\Vendor;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreVendorRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(
            Gate::denies('vendor_create'),
            response()->json(
                ['message' => 'This action is unauthorized.'],
                Response::HTTP_FORBIDDEN
            ),
        );

        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'string',
                'required', // Set Required Add Zuhaab
            ],
            'gender' => [
                'nullable',
                'in:' . implode(',', array_keys(Vendor::GENDER_RADIO)),
            ],
            'contact' => [
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
            ]
        ];
    }
}
