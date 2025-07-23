<?php

namespace Modules\Ticket\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Modules\Ticket\Rules\ExistsUserRule;

class StationUserRequest extends FormRequest
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
            'user_id' => [
                'required',
                new ExistsUserRule($this->station_id)
            ],
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     */
    public function messages(): array
    {
        return [
            'station_id.required' => 'El campo :attribute es obligatorio.',
            'user_id.required' => 'El campo :attribute es obligatorio.'
        ];
    }
}
