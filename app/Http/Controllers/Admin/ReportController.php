<?php

namespace App\Http\Controllers\Admin;

use App\Models\Report;
use App\Models\ReportDesign;
use App\Models\ReportSubData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CustomScript;

class ReportController
{
    public function index(Request $request)
    {
        $reportDesigns = ReportDesign::where('is_active', true)
                                   ->orderBy('name')
                                   ->get();

        $status = $request->input('status');
        $start = $request->input('date_from');
        $end = $request->input('date_to');
        $reportDesignId = $request->input('report_design_id');

        $query = Report::with(['reportDesign', 'creator'])->latest();

        if ($start) {
            $query->whereDate('created_at', '>=', $start);
        }

        if ($end) {
            $query->whereDate('created_at', '<=', $end);
        }

        if ($reportDesignId) {
            $query->where('report_design_id', $reportDesignId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $reports = $query->paginate(15);

        return view('reports.index', compact('reports', 'start', 'end', 'reportDesignId', 'reportDesigns'));
    }

    public function create()
    {
        $scripts = CustomScript::where('is_active', true)->get();
        $reportDesigns = ReportDesign::where('is_active', true)
                                   ->orderBy('name')
                                   ->get();

        return view('reports.select-design', compact('reportDesigns', 'scripts'));
    }

    public function createFromDesign(ReportDesign $reportDesign)
    {
        $scripts = CustomScript::where('is_active', true)->get();
        $reportDesign->load(['fields', 'subDesigns.fields']);
     
        $users = User::select('id', 'name')->orderBy('name')->get();
        
        return view('reports.create', compact('reportDesign', 'users', 'scripts'));
    }

    public function store(Request $request)
    {
        // dd($request->main_data);
        $reportDesign = ReportDesign::findOrFail($request->report_design_id);
        
        // Validate main fields
        $this->validateReportData($request, $reportDesign);

        try {
            DB::beginTransaction();

            // Create main report
            $report = Report::create([
                'report_design_id' => $reportDesign->id,
                'title' => $request->title,
                'data' => $request->main_data ?? [],
                'status' => $request->status ?? 'draft',
                'created_by' => Auth::id(),
            ]);

            // Save sub-report data
            if ($request->has('sub_data')) {
                $this->storeSubReportData($report, $request->sub_data);
            }

            DB::commit();

            return redirect()->route('reports.show', $report)
                           ->with('success', 'Report berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    public function show(Report $report)
    {
        $report->load([
            'reportDesign.fields',
            'reportDesign.subDesigns.fields',
            'subData.reportSubDesign',
            'creator'
        ]);

        return view('reports.show', compact('report'));
    }

    public function edit(Report $report)
    {
        $scripts = CustomScript::where('is_active', true)->get();
        $users = User::select('id', 'name')->orderBy('name')->get();

        $report->load([
            'reportDesign.fields',
            'reportDesign.subDesigns.fields',
            'subData.reportSubDesign',
            'creator'
        ]);

        // dd($report);

        return view('reports.edit', compact('report', 'users', 'scripts'));
    }

    public function update(Request $request, Report $report)
    {
        // $this->validateReportData($request, $report->reportDesign);

        try {
            DB::beginTransaction();

            // Update main report
            $report->update([
                'title' => $request->title,
                'data' => $request->main_data ?? [],
                'status' => $request->status ?? 'draft',
            ]);

            // Delete existing sub-data and recreate
            $report->subData()->delete();
            
            if ($request->has('sub_data')) {
                $this->storeSubReportData($report, $request->sub_data);
            }

            DB::commit();

            return redirect()->route('reports.show', $report)
                           ->with('success', 'Report berhasil diupdate!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    public function destroy(Report $report)
    {
        try {
            $report->delete(); // Will cascade delete sub data
            
            return redirect()->route('reports.index')
                           ->with('success', 'Report berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Store sub-report data
     */
    public function storeSubData(Report $report, Request $request)
    {
        $request->validate([
            'sub_design_id' => 'required|exists:report_sub_designs,id',
            'data' => 'required|array',
            'row_index' => 'integer|min:0'
        ]);

        $subData = ReportSubData::create([
            'report_id' => $report->id,
            'report_sub_design_id' => $request->sub_design_id,
            'data' => $request->data,
            'row_index' => $request->row_index ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'sub_data' => $subData,
            'message' => 'Sub-report data berhasil disimpan!'
        ]);
    }

    /**
     * Delete sub-report data
     */
    public function destroySubData(Report $report, ReportSubData $subData)
    {
        if ($subData->report_id !== $report->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $subData->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sub-report data berhasil dihapus!'
        ]);
    }

    /**
     * Validate report data based on design
     */
    private function validateReportData(Request $request, ReportDesign $reportDesign)
    {
        $rules = [
            // 'title' => 'nullable|string|max:255',
            'status' => 'in:draft,submitted,approved,rejected',
            'report_design_id' => 'required|exists:report_designs,id'
        ];

        // Validate main fields
        foreach ($reportDesign->fields as $field) {
            $fieldKey = "main_data.{$field->name}";
            
            if ($field->required) {
                $rules[$fieldKey] = 'required';
            }

            // Add type-specific validation
            switch ($field->type) {
                case 'number':
                    $rules[$fieldKey] = ($rules[$fieldKey] ?? '') . '|numeric';
                    break;
                case 'email':
                    $rules[$fieldKey] = ($rules[$fieldKey] ?? '') . '|email';
                    break;
                case 'date':
                    $rules[$fieldKey] = ($rules[$fieldKey] ?? '') . '|date';
                    break;
                case 'file':
                case 'image':
                    $rules[$fieldKey] = ($rules[$fieldKey] ?? '') . '|file';
                    if ($field->type === 'image') {
                        $rules[$fieldKey] .= '|image|max:2048';
                    }
                    break;
                case 'select':
                    if ($field->options) {
                        $validValues = collect($field->options)->pluck('value')->toArray();
                        $rules[$fieldKey] = ($rules[$fieldKey] ?? '') . '|in:' . implode(',', $validValues);
                    }
                    break;
            }
        }

        // Validate sub-report data
        foreach ($reportDesign->subDesigns as $subDesign) {
            foreach ($subDesign->fields as $subField) {
                $subFieldKey = "sub_data.{$subDesign->id}.*.{$subField->name}";
                
                if ($subField->required) {
                    $rules[$subFieldKey] = 'required';
                }

                // Add type-specific validation for sub fields
                switch ($subField->type) {
                    case 'number':
                        $rules[$subFieldKey] = ($rules[$subFieldKey] ?? '') . '|numeric';
                        break;
                    case 'email':
                        $rules[$subFieldKey] = ($rules[$subFieldKey] ?? '') . '|email';
                        break;
                    case 'date':
                        $rules[$subFieldKey] = ($rules[$subFieldKey] ?? '') . '|date';
                        break;
                    case 'select':
                        if ($subField->options) {
                            $validValues = collect($subField->options)->pluck('value')->toArray();
                            $rules[$subFieldKey] = ($rules[$subFieldKey] ?? '') . '|in:' . implode(',', $validValues);
                        }
                        break;
                }
            }
        }

        return $request->validate($rules);
    }

    /**
     * Store sub-report data from form submission
     */
    private function storeSubReportData(Report $report, array $subData)
    {
        foreach ($subData as $subDesignId => $rows) {
            if (!is_array($rows)) continue;

            foreach ($rows as $rowIndex => $rowData) {
                if (empty($rowData) || !is_array($rowData)) continue;

                ReportSubData::create([
                    'report_id' => $report->id,
                    'report_sub_design_id' => $subDesignId,
                    'data' => $rowData,
                    'row_index' => $rowIndex,
                ]);
            }
        }
    }

    /**
     * Export report to PDF/Excel
     */
    public function export(Report $report, $format = 'pdf')
    {
        $report->load([
            'reportDesign.fields',
            'reportDesign.subDesigns.fields',
            'subData.reportSubDesign'
        ]);

        switch ($format) {
            case 'pdf':
                return $this->exportToPdf($report);
            case 'excel':
                return $this->exportToExcel($report);
            default:
                return back()->withErrors(['error' => 'Format export tidak didukung']);
        }
    }

    private function exportToPdf(Report $report)
    {
        // Implement PDF export logic here
        // You can use libraries like DomPDF or TCPDF
        
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('reports.pdf.export', compact('report'));
        
        return $pdf->download("report-{$report->id}.pdf");
    }

    private function exportToExcel(Report $report)
    {
        // Implement Excel export logic here
        // You can use Laravel Excel package
        
        return Excel::download(new ReportExport($report), "report-{$report->id}.xlsx");
    }

    /**
     * Get report statistics
     */
    public function getStatistics(Request $request)
    {
        $query = Report::query();

        if ($request->has('design_id')) {
            $query->where('report_design_id', $request->design_id);
        }

        if ($request->has('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        $statistics = [
            'total_reports' => $query->count(),
            'draft_reports' => $query->where('status', 'draft')->count(),
            'submitted_reports' => $query->where('status', 'submitted')->count(),
            'approved_reports' => $query->where('status', 'approved')->count(),
            'rejected_reports' => $query->where('status', 'rejected')->count(),
        ];

        return response()->json($statistics);
    }

    /**
     * Bulk operations
     */
    public function bulkAction(Request $request)
    {
        // dd($request->all());
        $request->merge([
            'report_ids' => json_decode($request->report_ids, true)
        ]);

        $request->validate([
            'action' => 'required|in:delete,approve,reject,archive',
            'report_ids' => 'required|array|min:1',
            'report_ids.*' => 'exists:reports,id'
        ]);

        try {
            DB::beginTransaction();

            $reports = Report::whereIn('id', $request->report_ids);

            switch ($request->action) {
                case 'delete':
                    $reports->delete();
                    $message = 'Reports berhasil dihapus!';
                    break;
                case 'approve':
                    $reports->update(['status' => 'approved']);
                    $message = 'Reports berhasil di-approve!';
                    break;
                case 'reject':
                    $reports->update(['status' => 'rejected']);
                    $message = 'Reports berhasil di-reject!';
                    break;
                case 'archive':
                    $reports->update(['status' => 'archived']);
                    $message = 'Reports berhasil di-archive!';
                    break;
            }

            DB::commit();

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}