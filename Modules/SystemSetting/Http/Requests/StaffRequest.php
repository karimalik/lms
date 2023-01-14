<?php

namespace Modules\SystemSetting\Http\Requests;

use App\Traits\ValidationMessage;
use Illuminate\Foundation\Http\FormRequest;
use function trans;

class StaffRequest extends FormRequest
{
    use ValidationMessage;
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
     * @return array
     */
    public function rules()
    {
        return [
              "employee_id" => "required_if:role_type,!=,'system_user'",
              "name" => "required",
              "username" => "sometimes|nullable|numeric|unique:users,username,".$this->user_id,
              "email" => "required|unique:users,email,".$this->user_id,
              "password" => "required|min:8",
              "department_id" => "required|numeric",
              "role_id" => "required|numeric",
//              "date_of_birth" => "sometimes|nullable",
//              "current_address" => "required_if:role_type,!=,'system_user'",
//              "permanent_address" => "required_if:role_type,!=,'system_user'",
//              "bank_name" => "required_if:role_type,!=,1",
//              "bank_branch_name" => "required_if:role_type,!=,'system_user'",
//              "bank_account_name" => "required_if:role_type,!=,'system_user'",
//              "bank_account_no" => "required_if:role_type,!=,system_user",
//              "date_of_joining" => "required_if:role_type,!=,'system_user'",
//              "basic_salary" => "required_if:role_type,!=,'system_user'",
//              "employment_type" => "required_if:role_type,!=,'system_user'",
              'photo' => 'nullable|mimes:jpeg,jpg,png',
              'signature_photo' => 'nullable|mimes:jpeg,jpg,png',
        ];
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'username'        => trans('retailer.Phone'),
        ];
    }
}
