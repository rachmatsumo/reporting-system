<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ReportSummarySheet implements FromView, WithTitle, WithDrawings, WithEvents
{
    protected $reports;
    protected $drawings = [];

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

    public function drawings(): array
    {
        $drawings = [];

        foreach ($this->drawings as $d) {
            $drawing = new Drawing();
            $drawing->setPath($d['path']);
            $drawing->setHeight(60);
            $drawing->setCoordinates($d['coordinates']);
            $drawing->setOffsetX(10);
            $drawing->setOffsetY(5);
            $drawings[] = $drawing;
        }

        return $drawings;
    } 
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Dapatkan kolom tertinggi
                $highestColumn = $sheet->getHighestColumn();
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);

                    // Bungkus teks + posisi teks rata kiri atas
                    $sheet->getStyle($colLetter)->getAlignment()
                        ->setWrapText(true)
                        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

                    // Batas maksimal lebar kolom (misal 20 karakter)
                    $sheet->getColumnDimension($colLetter)->setWidth(15);
                }

                // (Opsional) semua cell punya border tipis biar rapi
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            },
        ];
    }
}

