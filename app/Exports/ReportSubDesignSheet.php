<?php

namespace App\Exports;

use App\Models\ReportSubDesign;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ReportSubDesignSheet implements FromView, WithTitle, WithDrawings, WithEvents
{
    protected $reports;
    protected $subDesign;
    protected $drawings = [];

    public function __construct($reports, ReportSubDesign $subDesign)
    {
        $this->reports = $reports;
        $this->subDesign = $subDesign;
    }

    public function view(): View
    {
        $rowCounter = 2; // baris awal (karena header di baris 1)
        $colSignatureMap = [];

        foreach ($this->reports as $report) {
            $rows = $report->subData
                ->where('report_sub_design_id', $this->subDesign->id)
                ->sortBy('row_index');

            foreach ($rows as $row) {
                $colIndex = 3; // mulai dari kolom C (karena A=Report ID, B=Title)

                foreach ($this->subDesign->fields as $field) {
                    $v = $row->data[$field->name] ?? null;

                    if ($field->type === 'signing' && $v && Str::startsWith($v, 'data:image')) {
                        // Simpan sementara file base64 jadi PNG
                        $imagePath = storage_path("app/public/sign_{$report->id}_{$field->id}_{$rowCounter}.png");
                        $imageData = explode(',', $v)[1];
                        file_put_contents($imagePath, base64_decode($imageData));

                        // Tentukan kolom (A=1, B=2, C=3, dst)
                        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);

                        // Simpan posisi gambar agar bisa ditarik di drawings()
                        $this->drawings[] = [
                            'path' => $imagePath,
                            'coordinates' => "{$colLetter}{$rowCounter}",
                        ];
                    }

                    $colIndex++;
                }

                $rowCounter++;
            }
        }

        return view('exports.report-sub-design', [
            'subDesign' => $this->subDesign,
            'reports' => $this->reports
        ]);
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

    public function title(): string
    {
        return substr($this->subDesign->name, 0, 31);
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
                    $sheet->getColumnDimension($colLetter)->setWidth(20);
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
