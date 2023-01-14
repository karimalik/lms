<?php

namespace App\Imports;


use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\StudentSetting\Entities\StudentImportTemporary;

class StudentImport implements ToModel, WithStartRow, WithHeadingRow
{

    private $settings;

    public function __construct($field_settings)
    {
        $this->settings = $field_settings;

    }

    public function model(array $row)
    {
        if (isset($row['name']) && $row['email']) {
            $student = new StudentImportTemporary();
            $student->name = @$row['name'];
            $student->email = @$row['email'];
            $student->phone = @$row['phone'];
            $student->country = @$row['country'];

            if ($this->settings->show_company == 1) {
                $student->company = @$row['company'];
            }
            if ($this->settings->show_gender == 1) {
                $student->gender = @$row['gender'];
            }
            if ($this->settings->show_student_type == 1) {
                $student->student_type = @$row['student_type'];
            }
            if ($this->settings->show_identification_number == 1) {
                $student->identification_number = @$row['identification_number'];
            }

            if ($this->settings->show_job_title == 1) {
                $student->job_title = @$row['job_title'];
            }
            if (!empty($row['dob'])) {
                try {
                    $birthday = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['dob'])->format('m/d/Y');
                } catch (\Exception $e) {
                    $birthday = Carbon::parse($row['dob'])->format('m/d/Y')??null;
                }
                $student->dob = $birthday;
            }
            $student->created_by = Auth::user()->id;
//            $student->referral =  generateUniqueId();
//            $student->language_id = Settings('language_id') ?? '19';
//            $student->language_name = Settings('language_name') ?? 'English';
//            $student->language_code = Settings('language_code') ?? 'en';
//            $student->language_rtl = Settings('language_rtl') ?? 'en';
//            $student->country = Settings('country_id') ?? 'en';

            $student->save();
            return $student;
        }


    }

    public function startRow(): int
    {
        return 2;
    }

    public function headingRow(): int
    {
        return 1;
    }
}
