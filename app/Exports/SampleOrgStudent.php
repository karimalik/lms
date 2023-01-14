<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SampleOrgStudent implements WithMultipleSheets, FromView, WithTitle,WithStyles
{
    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new SampleOrgStudent();
        $sheets[] = new OrgStudentGuideline();

        return $sheets;
    }

    public function view(): View
    {
        return view('org::exports.sample-student');
    }

    public function title(): string
    {
        return 'Import';
    }
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
