<?php

namespace App\Exports;


use App\StudentCustomField;
use Maatwebsite\Excel\Concerns\FromCollection;

class OfflineStudentExport implements FromCollection
{

    public function collection()
    {
        $custom_field = StudentCustomField::getData();

        $excel_column = [];

        if ($custom_field->show_name == 1) {
            $excel_column[] = 'name';
        }
        $excel_column[] = 'email';
        $excel_column[] = 'phone';

        if ($custom_field->show_company == 1) {
            $excel_column[] = 'company';
        }
        if ($custom_field->show_gender == 1) {
            $excel_column[] = 'gender';
        }
        if ($custom_field->show_student_type == 1) {
            $excel_column[] = 'student_type';
        }
        if ($custom_field->show_identification_number == 1) {
            $excel_column[] = 'identification_number';
        }
        if ($custom_field->show_job_title == 1) {
            $excel_column[] = 'job_title';
        }
        $excel_column[] = 'dob';
        $excel_column[] = 'country';


        $collection = collect([$excel_column]);

        return $collection;
    }
}
