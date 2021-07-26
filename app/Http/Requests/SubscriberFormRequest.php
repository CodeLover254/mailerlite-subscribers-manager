<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscriberFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email'=>'required|email',
            'name'=>'required|max:50',
            'country'=>'required|max:50',
        ];
    }
}
