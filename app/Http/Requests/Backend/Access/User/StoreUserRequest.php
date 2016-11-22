<?php

namespace App\Http\Requests\Backend\Access\User;

use App\Http\Requests\Request;

/**
 * Class StoreUserRequest
 * @package App\Http\Requests\Backend\Access\User
 */
class StoreUserRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return access()->allow('manage-users');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'                  => 'required',
            'email'                 => 'required|email|unique:users|validate_email_company',
            'password'              => 'required|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{6,}$/|min:6|confirmed',
            'password_confirmation' => 'required|min:6',
            'assignees_roles'       => 'required'
        ];
    }
}
