<?php

namespace InteractiveSolutions\HoneycombNewCore\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HCUserRequest extends FormRequest
{
    /**
     * Get request inputs
     *
     * @return array
     */
    public function getInputData(): array
    {
        switch ($this->method()) {
            case 'DELETE' :
                return [
                    'list' => $this->input('list'),
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
                    'list' => $this->input('list'),
                ];
        }
    }

    public function getListFields()
    {
        return $this->all();
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = [];

        switch ($this->method()) {
            case 'DELETE' :
                $rules = [
                    'list' => 'required',
                ];

            case 'POST' :
                $rules = [];

            case 'PUT' :
                $rules = [];

            case 'PATCH' :
                $rules = [];
        }

        return $rules;
    }
}
