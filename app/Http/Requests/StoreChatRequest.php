<?php

namespace App\Http\Requests;
use App\Models\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreChatRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $user = new User();
        return [


            'user_id' => "required|exists:{$user},id",
            'name' => 'nullable',
            'is_private' => 'nullable|boolean'

        ];
    }
}
