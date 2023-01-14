<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Modules\OrgSubscription\Entities\OrgSubscriptionCheckout;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrgSubscriptionEnrollmentCheckoutExport implements FromView,WithStyles
{
    public function view(): View
    {
        $students = OrgSubscriptionCheckout:: with('plan', 'user')->latest()->get();
        return view('orgsubscription::enrollment.export',compact('students'));
    }
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
