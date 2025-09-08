<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ReportDesign;
use App\Models\ReportField;
use App\Models\ReportSubDesign;
use App\Models\ReportSubField;

class ReportDesignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ReportDesign::create([
            'name' => 'Daily Report',
            'description' => 'Laporan harian dan checklist',
            'is_active' => 1,
        ]);

        ReportField::create([
            'report_design_id' => 1,
            'name' => 'tanggal',
            'label' => 'Tanggal',
            'type' => 'date',
            'required' => 1,
            'order' => 0,
            'order_index' => 0,
        ]);

        ReportSubDesign::create([
            'report_design_id' => 1,
            'name' => 'Data Fasilitas',
            'description' => 'Informasi kondisi fasilitas kerja',
            'is_active' => 1,
            'type' => 'form',
        ]);

        $report_sub_fields = [
            [
                'report_sub_design_id' => 1,
                'name' => 'area',
                'label' => 'Area',
                'type' => 'text',
                'required' => 1, 
                'order_index' => 0,
                'options' => null,
            ],
            [
                'report_sub_design_id' => 1,
                'name' => 'map',
                'label' => 'Map',
                'type' => 'map',
                'required' => 1, 
                'order_index' => 0,
                'options' => null,
            ],
            [
                'report_sub_design_id' => 1,
                'name' => 'status',
                'label' => 'Status',
                'type' => 'select',
                'required' => 1, 
                'order_index' => 0,
                'options' => '[
                                {
                                    "label": "OK",
                                    "value": "ok"
                                },
                                {
                                    "label": "Not OK",
                                    "value": "not_ok"
                                }
                            ]'
            ],
        ];

        ReportSubField::insert($report_sub_fields);
    }
}
