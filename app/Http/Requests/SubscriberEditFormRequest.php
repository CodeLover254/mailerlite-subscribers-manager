<?php


namespace App\Http\Requests;


class SubscriberEditFormRequest extends SubscriberFormRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        unset($rules['email']);
        return $rules;
    }
}
