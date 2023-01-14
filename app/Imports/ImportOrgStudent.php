<?php

namespace App\Imports;

use App\Jobs\OrgStudentSignupNotification;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Validators\Failure;

class ImportOrgStudent implements WithStartRow, WithHeadingRow, WithValidation, OnEachRow, SkipsOnFailure
{
    use SkipsFailures;

//ToModel,
    public function rules(): array
    {
        return [
            'email' => 'required|unique:users,email',
            'employee_id' => 'required|unique:users,employee_id',
            'position_code' => 'required',
            'name' => 'required',
            'org_chart_code' => 'required',
        ];

    }

    public function customValidationMessages()
    {
        return [
            'email.unique' => trans('org.The Email has already been taken'),
            'employee_id.unique' => trans('org.The Employee ID has already been taken'),
            'email.required' => trans('org.Email is required'),
            'name.required' => trans('org.Name is required'),
            'employee_id.required' => trans('org.Employee ID is required'),
            'position_code.required' => trans('org.Position Code is required'),
            'org_chart_code.required' => trans('org.Org Chart Code is required'),
        ];
    }

    public function startRow(): int
    {
        return 2;
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function onRow(Row $row)
    {
        $row = $row->toArray();

        $name = $row['name'];
        $org_chart_code = $row['org_chart_code'];
        $position_code = $row['position_code'];
        $email = $row['email'];
        $employee_id = $row['employee_id'];
        $birthday = transformExcelDate($row['birthday']) ?? null;
        $start_working_date = transformExcelDate($row['start_working_date']) ?? null;
        $gender = $row['gender'] ?? null;
        $phone = $row['phone'] ?? null;
        $phone = !empty($phone) ? substr($phone, 0, 1) != 0 ? '0' . $phone : $phone : null;

        $password = Str::random(8);

        $exist = null;

        if (!empty($phone)) {
            $exist = User::where('phone', $phone)->first();
            if ($exist) {
                Toastr::error($phone.' '.trans('org.Phone Number has already been taken'), trans('common.Failed'));
            }
        }
        if (!$exist) {
            $new_student = User::create([
                'name' => $name,
                'org_chart_code' => $org_chart_code,
                'org_position_code' => $position_code,
                'email' => strtolower($email),
                'employee_id' => $employee_id,
                'dob' => $birthday,
                'start_working_date' => $start_working_date,
                'gender' => $gender,
                'phone' => $phone,
                'email_verified_at' => now(),
                'password' => Hash::make($password),
                'referral' => generateUniqueId(),
                'language_id' => Settings('language_id') ?? '19',
                'language_name' => Settings('language_name') ?? 'English',
                'language_code' => Settings('language_code') ?? 'en',
                'language_rtl' => Settings('language_rtl') ?? '0',
                'country' => Settings('country_id'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            if ($new_student) {
                OrgStudentSignupNotification::dispatch($new_student, $password);
            }
        }


    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            foreach ($failure->errors() as $error) {
                Toastr::error(trans('org.Row no') . $failure->row() . ', ' . $error, trans('common.Failed'));
            }
        }

    }
}
