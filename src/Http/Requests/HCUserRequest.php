<?php

namespace InteractiveSolutions\HoneycombNewCore\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HCUserRequest extends FormRequest
{
    /**
     * * Get create inputs
     *
     * @return array
     */
    public function getInputData(): array
    {
        switch ($this->method())
        {
            case 'DELETE' :
                return [
                    'list' => $this->input('list')
                ];

            case 'POST' :
                return [
                    'record' => [
                        'email' => $this->input('email'),
                    ],
                ];

            case 'PUT' :
                return [
                    'record' => [
                        'email' => $this->input('email'),
                    ],
                ];

            case 'PATCH' :
                return [
                    'list' => $this->input('list')
                ];
        }

        return $return;
    }

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
     * @return array
     */
    public function rules()
    {
        switch ($this->method())
        {
            case 'DELETE' :
                return [
                    'list' => 'required',
                ];

            case 'POST' :
                return [

                ];

            case 'PUT' :
                return [

                ];

            case 'PATCH' :
                return [

                ];
        }
    }
}
