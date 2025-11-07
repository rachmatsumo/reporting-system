<?php

namespace App\Exports;

use App\Models\ReportSubDesign;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportListExport implements WithMultipleSheets
{
    protected $reports;

    public function __construct(Collection $reports)
    {
        $this->reports = $reports;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Sheet 1 — Summary report
        $sheets[] = new ReportSummarySheet($this->reports);

        // Sub-report sheets
        $designId = $this->reports->first()->report_design_id;

        $subDesigns = ReportSubDesign::where('report_design_id', $designId)
                                     ->orderBy('order_index')
                                     ->get();

        foreach ($subDesigns as $subDesign) {
            $sheets[] = new ReportSubDesignSheet($this->reports, $subDesign);
        }

        return $sheets;
    }
}
