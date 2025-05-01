<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TenantUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // For now, we'll authorize all requests
        // In a real-world application, you'll want to check permissions
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'name' => 'string|max:255',
            'email' => 'email|max:255',
            'password' => 'string|min:8',
            'role' => 'string|in:admin,user',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ];

        // Different rules based on request method
        if ($this->isMethod('POST')) {
            // For creating users
            $rules['name'] .= '|required';
            $rules['email'] .= '|required|unique:tenant.tenant_users,email';
            $rules['password'] .= '|required';
        } elseif ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            // For updating users
            $rules['name'] = 'sometimes|' . $rules['name'];
            $rules['email'] = 'sometimes|' . $rules['email'];
            
            // Add unique check for email but ignore the current user
            if ($this->route('id')) {
                $rules['email'] .= '|' . Rule::unique('tenant.tenant_users')->ignore($this->route('id'));
            }
            
            $rules['password'] = 'sometimes|' . $rules['password'];
            $rules['role'] = 'sometimes|' . $rules['role'];
            $rules['position'] = 'sometimes|' . $rules['position'];
            $rules['phone'] = 'sometimes|' . $rules['phone'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already taken.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'role.in' => 'The role must be either "admin" or "user".',
        ];
    }
} 