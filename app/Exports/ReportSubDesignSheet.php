<?php

namespace App\Exports;

use App\Models\ReportSubDesign;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReportSubDesignSheet implements FromView, WithTitle
{
    protected $reports;
    protected $subDesign;

    public function __construct($reports, ReportSubDesign $subDesign)
    {
        $this->reports = $reports;
        $this->subDesign = $subDesign;
    }

    public function view(): View
    {
        return view('exports.report-sub-design', [
            'subDesign' => $this->subDesign,
            'reports' => $this->reports
        ]);
    }

    public function title(): string
    {
        return substr($this->subDesign->name, 0, 31); // nama sheet max 31 chars
    }
}
