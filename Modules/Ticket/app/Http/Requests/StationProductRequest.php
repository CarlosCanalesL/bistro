<?php

namespace Modules\Ticket\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Modules\Ticket\Rules\ExistsProductRule;

class StationProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'station_id' => 'required',
            'product_id' => [
                'required',
                new ExistsProductRule($this->station_id)
            ],
            'status' => 'required'
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'El campo :attribute es obligatorio.',
            'station_id.required' => 'El campo :attribute es obligatorio.',
            'status.required' => 'El campo :attribute es obligatorio.'
        ];
    }
}
