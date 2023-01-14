<?php

namespace App\Exports;

use App\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AllOrgStudent implements FromView,WithStyles
{
    public function view(): View
    {
        $students = User::where('role_id', 3)->where('teach_via', 1)->get();
        return view('org::exports.all_students', [
            'students' => $students
        ]);
    }
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
