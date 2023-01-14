<?php

namespace App\Exports;

use App\Models\QuestionBank;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class ExportSampleQuestionBank implements FromView
{
    public function view(): View
    {
        return view('quiz::exports.sample-bulk-question');
    }
}
