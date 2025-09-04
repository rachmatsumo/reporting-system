<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReportType;
use App\Models\ReportField;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        // Buat jenis laporan A
        // $laporanA = ReportType::create([
        //     'name' => 'Laporan A',
        //     'description' => 'Laporan kegiatan harian'
        // ]);

        // // Field untuk laporan A
        // ReportField::insert([
        //     [
        //         'report_type_id' => $laporanA->id,
        //         'field_name' => 'tanggal',
        //         'field_label' => 'Tanggal Laporan',
        //         'field_type' => 'date',
        //         'is_required' => true,
        //         'order' => 1,
        //     ],
        //     [
        //         'report_type_id' => $laporanA->id,
        //         'field_name' => 'remark',
        //         'field_label' => 'Keterangan',
        //         'field_type' => 'textarea',
        //         'is_required' => false,
        //         'order' => 2,
        //     ],
        //     [
        //         'report_type_id' => $laporanA->id,
        //         'field_name' => 'lampiran',
        //         'field_label' => 'Foto Lampiran',
        //         'field_type' => 'file',
        //         'is_required' => false,
        //         'order' => 3,
        //     ],
        // ]);

        // // Buat jenis laporan B
        // $laporanB = ReportType::create([
        //     'name' => 'Laporan B',
        //     'description' => 'Laporan aktivitas shift'
        // ]);

        // ReportField::insert([
        //     [
        //         'report_type_id' => $laporanB->id,
        //         'field_name' => 'aktivitas',
        //         'field_label' => 'Aktivitas',
        //         'field_type' => 'text',
        //         'is_required' => true,
        //         'order' => 1,
        //     ],
        //     [
        //         'report_type_id' => $laporanB->id,
        //         'field_name' => 'jam',
        //         'field_label' => 'Jam Mulai',
        //         'field_type' => 'time',
        //         'is_required' => true,
        //         'order' => 2,
        //     ],
        //     [
        //         'report_type_id' => $laporanB->id,
        //         'field_name' => 'jumlah',
        //         'field_label' => 'Jumlah Personel',
        //         'field_type' => 'number',
        //         'is_required' => false,
        //         'order' => 3,
        //     ],
        // ]);
    }
}
