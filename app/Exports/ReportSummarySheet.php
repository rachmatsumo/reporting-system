<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReportSummarySheet implements FromView, WithTitle
{
    protected $reports;

    public function __construct($reports)
    {
        $this->reports = $reports;
    }

    public function view(): View
    {
        return view('exports.report-summary', [
            'reports' => $this->reports
        ]);
    }

    public function title(): string
    {
        return 'Summary Report';   //  Nama Sheet
    }
}

