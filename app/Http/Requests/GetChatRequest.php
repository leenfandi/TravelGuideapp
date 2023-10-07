<?php

namespace App\Http\Requests;
use App\Models\User;

use Illuminate\Foundation\Http\FormRequest;

class GetChatRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            //
            'is_private' => 'nullable|boolean'
        ];
    }
}