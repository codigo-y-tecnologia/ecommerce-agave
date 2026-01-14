<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Auth\Access\AuthorizationException;

class StoreRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {

        if (! $this->user()->can('gestionar_permisos')) {
            return false;
        }

        if (in_array($this->name, ['superadmin'])) {
            return false;
        }

        return true;
    }

    protected function failedAuthorization()
    {
        throw new AuthorizationException(
            'No puedes crear ni modificar este rol porque es crítico para el sistema.'
        );
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'name' => strtolower(trim($this->name)),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:50|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.unique'   => 'Este rol ya existe.',
            'permissions.*.exists' => 'Uno de los permisos seleccionados no es válido.',
        ];
    }
}
