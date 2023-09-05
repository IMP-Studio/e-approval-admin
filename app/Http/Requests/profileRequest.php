<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class profileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules()
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'avatar' => 'required|image|mimes:jpeg,png,jpg',
            'id_number' => 'required',
            'position_id' => 'required',
            'division_id' => 'required',
            'gender' => 'required',
            'address' => 'required',
            'birth_date' => 'required',
        ];
    }
}
