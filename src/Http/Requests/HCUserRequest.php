<?php

declare(strict_types = 1);

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
            case 'DELETE':
                return [
                    'list' => $this->input('list'),
                ];
                break;

            case 'POST':
                return [
                    'record' => [
                        'email' => $this->input('email'),
                    ],
                ];
                break;

            case 'PUT':
                return [
                    'record' => [
                        'email' => $this->input('email'),
                    ],
                ];
                break;

            case 'PATCH':
                return [
                    'list' => $this->input('list'),
                ];
                break;
        }

        return [];
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
        switch ($this->method()) {
            case 'DELETE':
                return [
                    'list' => 'required',
                ];
                break;

            case 'POST':
                return [];
                break;

            case 'PUT':
                return [];
                break;

            case 'PATCH':
                return [];
                break;
        }

        return [];
    }
}
