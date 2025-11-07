<?php

namespace App\Http\Controllers\Admin;

use App\Models\ReportDesign;
use App\Models\ReportField;
use App\Models\ReportSubDesign;
use App\Models\ReportSubField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportDesignController
{

    public function index()
    {
        $reportDesigns = ReportDesign::with('fields')->get();
        return view('admin.report-design.index', compact('reportDesigns'));
    }

    public function create()
    {
        return view('admin.report-design.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            
            // Main fields validation
            'main_fields.*.label' => 'required|string|max:255',
            'main_fields.*.type' => 'required|in:text,textarea,textarea_rich,number,file,image,date,time,month,year,checkbox,select,map,personnel,attendance,signing',
            'main_fields.*.required' => 'nullable|boolean',
            'main_fields.*.default_value' => 'nullable|string',
            'main_fields.*.options.*.value' => 'required_if:main_fields.*.type,select|string',
            'main_fields.*.options.*.label' => 'required_if:main_fields.*.type,select|string',
            
            // Sub reports validation
            'sub_reports.*.name' => 'nullable|string|max:255',
            'sub_reports.*.type' => 'nullable|in:form,checklist,table,custom',
            'sub_reports.*.description' => 'nullable|string',
            'sub_reports.*.fields.*.label' => 'required|string|max:255',
            'sub_reports.*.fields.*.type' => 'required|in:text,textarea,textarea_rich,number,file,image,date,time,month,year,checkbox,select,map,personnel,attendance,signing',
            'sub_reports.*.fields.*.required' => 'nullable|boolean',
            'sub_reports.*.fields.*.default_value' => 'nullable|string',
            'sub_reports.*.fields.*.options.*.value' => 'required_if:sub_reports.*.fields.*.type,select|string',
            'sub_reports.*.fields.*.options.*.label' => 'required_if:sub_reports.*.fields.*.type,select|string',
        ]);

        try {
            DB::beginTransaction();

            // Create main report design
            $reportDesign = ReportDesign::create([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => true,
            ]);

            // Save main fields
            if ($request->has('main_fields')) {
                $this->saveFields($request->main_fields, $reportDesign->id);
            }

            // Save sub reports and their fields
            if ($request->has('sub_reports')) {
                $this->saveSubReports($request->sub_reports, $reportDesign->id);
            }

            DB::commit();

            return redirect()->route('report-designs.index')
                           ->with('success', 'Report design berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    private function saveFields($fields, $reportDesignId)
    {
        foreach ($fields as $index => $fieldData) {
            if (empty($fieldData['label'])) continue;

            $fieldName = $fieldData['name'] ?? Str::slug($fieldData['label'], '_');
            
            $options = null;
            if ($fieldData['type'] === 'select' && isset($fieldData['options'])) {
                $options = $fieldData['options'];
            }
            // dd($options);

            ReportField::create([
                'report_design_id' => $reportDesignId,
                'name' => $fieldName,
                'label' => ucwords($fieldData['label']),
                'type' => $fieldData['type'],
                'required' => isset($fieldData['required']) ? (bool)$fieldData['required'] : false,
                'default_value' => $fieldData['default_value'] ?? null,
                'options' => $options,
                'order_index' => $fieldData['order_index'] ?? $index,
            ]);
        }
    }

    private function saveSubReports($subReports, $reportDesignId)
    {
        foreach ($subReports as $index => $subReportData) {
            if (empty($subReportData['name'])) continue;

            // Create sub report design
            $subReportDesign = ReportSubDesign::create([
                'report_design_id' => $reportDesignId,
                'name' => $subReportData['name'],
                'description' => $subReportData['description'] ?? null,
                'type' => $subReportData['type'] ?? 'form',
                'order_index' => $subReportData['order_index'] ?? $index,
                'is_active' => true,
            ]);

            // Save sub report fields
            if (isset($subReportData['fields'])) {
                $this->saveSubFields($subReportData['fields'], $subReportDesign->id);
            }
        }
    }

    private function saveSubFields($fields, $subReportDesignId)
    {
        foreach ($fields as $index => $fieldData) {
            if (empty($fieldData['label'])) continue;

            $fieldName = $fieldData['name'] ?? Str::slug($fieldData['label'], '_');
            
            $options = [];
            if ($fieldData['type'] === 'select' && isset($fieldData['options'])) {
                $options = $fieldData['options'];
            }

            ReportSubField::create([
                'report_sub_design_id' => $subReportDesignId,
                'name' => $fieldName,
                'label' => ucwords($fieldData['label']),
                'type' => $fieldData['type'],
                'required' => isset($fieldData['required']) ? (bool)$fieldData['required'] : false,
                'default_value' => $fieldData['default_value'] ?? null,
                'options' => $options,
                'order_index' => $fieldData['order_index'] ?? $index,
            ]);
        }
    }

    public function show(ReportDesign $reportDesign)
    {
        $reportDesign->load([
            'fields' => function($query) {
                $query->orderBy('order_index');
            },
            'subDesigns' => function($query) {
                $query->orderBy('order_index')->with(['fields' => function($subQuery) {
                    $subQuery->orderBy('order_index');
                }]);
            }
        ]);

        return view('report-design.show', compact('reportDesign'));
    }

    public function edit(ReportDesign $reportDesign)
    {
        $reportDesign->load([
            'fields' => function($query) {
                $query->orderBy('order_index');
            },
            'subDesigns' => function($query) {
                $query->orderBy('order_index')->with(['fields' => function($subQuery) {
                    $subQuery->orderBy('order_index');
                }]);
            }
        ]);

        return view('admin.report-design.edit', compact('reportDesign'));
    }

    public function update(Request $request, ReportDesign $reportDesign)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Update main report design
            $reportDesign->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            // ====== HANDLE MAIN FIELDS ======
            $existingFieldIds = $reportDesign->fields()->pluck('id')->toArray();
            $submittedFieldIds = collect($request->main_fields)->pluck('id')->filter()->toArray();

            // Hapus field yang tidak ada di request
            $reportDesign->fields()->whereNotIn('id', $submittedFieldIds)->delete();
            
            // Loop setiap main field
            foreach ($request->main_fields ?? [] as $field) {
                $fieldName = $field['name'] ?? Str::slug($field['label'], '_');
                if (!empty($field['id'])) {
                    // UPDATE field lama
                    $reportDesign->fields()->where('id', $field['id'])->update([
                        'name'          => $fieldName,
                        'label'         => $field['label'],
                        'type'          => $field['type'],
                        'required'      => !empty($field['required']),
                        'default_value' => $field['default_value'] ?? null,
                        'options'       => $field['options'] ?? null,
                    ]);
                } else {
                    // CREATE field baru
                    
                    $reportDesign->fields()->create([
                        'name'          => $fieldName,
                        'label'         => $field['label'],
                        'type'          => $field['type'],
                        'required'      => !empty($field['required']),
                        'default_value' => $field['default_value'] ?? null,
                        'options'       => $field['options'] ?? null,
                    ]);
                }
            }

            // ====== HANDLE SUB REPORTS ======
            $existingSubIds = $reportDesign->subDesigns()->pluck('id')->toArray();
            $submittedSubIds = collect($request->sub_reports)->pluck('id')->filter()->toArray();

            // Hapus subreport yang dihapus
            $reportDesign->subDesigns()->whereNotIn('id', $submittedSubIds)->delete();

            foreach ($request->sub_reports ?? [] as $subReportData) {
                if (!empty($subReportData['id'])) {
                    // Update sub report
                    $subReport = $reportDesign->subDesigns()->find($subReportData['id']);
                    $subReport->update([
                        'name'        => $subReportData['name'],
                        'type'        => $subReportData['type'],
                        'description' => $subReportData['description'],
                        'order_index' => $subReportData['order_index'] ?? 0,
                    ]);
                } else {
                    // Create sub report baru
                    $subReport = $reportDesign->subDesigns()->create([
                        'name'        => $subReportData['name'],
                        'type'        => $subReportData['type'],
                        'description' => $subReportData['description'],
                        'order_index' => $subReportData['order_index'] ?? 0,
                    ]);
                }

                // ====== HANDLE SUB FIELDS ======
                $existingSubFieldIds = $subReport->fields()->pluck('id')->toArray();
                $submittedSubFieldIds = collect($subReportData['fields'] ?? [])->pluck('id')->filter()->toArray();

                // Hapus sub fields yang hilang
                $subReport->fields()->whereNotIn('id', $submittedSubFieldIds)->delete();

                foreach ($subReportData['fields'] ?? [] as $field) {
                    $fieldName = $field['name'] ?? Str::slug($field['label'], '_');
                    if (!empty($field['id'])) {
                        // Update field lama
                        $subReport->fields()->where('id', $field['id'])->update([
                            'name'          => $fieldName,
                            'label'         => $field['label'],
                            'type'          => $field['type'],
                            'required'      => !empty($field['required']),
                            'default_value' => $field['default_value'] ?? null,
                            'options'       => $field['options'] ?? null,
                            'order_index'   => $field['order_index'] ?? 0,
                        ]);
                    } else {
                        // Create field baru
                        $subReport->fields()->create([
                            'name'          => $fieldName,
                            'label'         => $field['label'],
                            'type'          => $field['type'],
                            'required'      => !empty($field['required']),
                            'default_value' => $field['default_value'] ?? null,
                            'options'       => $field['options'] ?? null,
                            'order_index'   => $field['order_index'] ?? 0,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('report-designs.index')
                ->with('success', 'Report design berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    } 

    public function destroy(ReportDesign $reportDesign)
    {
        try {
            $reportDesign->delete(); // Will cascade delete fields and sub designs
            
            return redirect()->route('report-designs.index')
                           ->with('success', 'Report design berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Get sub report data for AJAX requests
     */
    public function getSubReportData(ReportSubDesign $subReportDesign)
    {
        $subReportDesign->load(['fields' => function($query) {
            $query->orderBy('order_index');
        }]);

        return response()->json($subReportDesign);
    }

    /**
     * Clone a report design
     */
    public function clone(ReportDesign $reportDesign)
    {
        try {
            DB::beginTransaction();

            // Clone main report design
            $newReportDesign = $reportDesign->replicate();
            $newReportDesign->name = $reportDesign->name . ' (Copy)';
            $newReportDesign->save();

            // Clone main fields
            foreach ($reportDesign->fields as $field) {
                $newField = $field->replicate();
                $newField->report_design_id = $newReportDesign->id;
                $newField->save();
            }

            // Clone sub reports and their fields
            foreach ($reportDesign->subDesigns as $subDesign) {
                $newSubDesign = $subDesign->replicate();
                $newSubDesign->report_design_id = $newReportDesign->id;
                $newSubDesign->save();

                // Clone sub fields
                foreach ($subDesign->fields as $subField) {
                    $newSubField = $subField->replicate();
                    $newSubField->report_sub_design_id = $newSubDesign->id;
                    $newSubField->save();
                }
            }

            DB::commit();

            return redirect()->route('report-designs.edit', $newReportDesign)
                           ->with('success', 'Report design berhasil di-clone!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}