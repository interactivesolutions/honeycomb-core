<?php

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombCore\Http\Requests;

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

    /**
     * Get personal info
     *
     * @return array
     */
    public function getPersonalData()
    {
        return [
            'first_name' => $this->input('first_name'),
            'last_name' => $this->input('last_name'),
        ];
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
            case 'POST':
                return [
                    'email' => 'required|email|unique:hc_users,email|min:5',
                    'password' => 'required|min:5',
                ];
                break;

            case 'PUT':

                $userId = $this->segment(4);

                return [
                    'email' => 'required|email|min:5|unique:hc_users,email,' . $userId,
                    'roles' => 'required',
                    'password' => 'nullable|min:5|confirmed',
                    'password_confirmation' => 'required_with:password|nullable|min:5',
                ];
                break;

            case 'PATCH':
                return [];
                break;

            case 'DELETE':
                return [
                    'list' => 'required',
                ];
                break;
        }

        return [];
    }
}
