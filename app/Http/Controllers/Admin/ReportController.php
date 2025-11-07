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
use App\Exports\ReportListExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

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
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10); // default 10

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

        //  SEARCH: Cari di title, creator name, atau report design name
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%$search%")
                ->orWhereHas('creator', function($cq) use ($search) {
                    $cq->where('name', 'LIKE', "%$search%");
                })
                ->orWhereHas('reportDesign', function($dq) use ($search) {
                    $dq->where('name', 'LIKE', "%$search%");
                });
            });
        }
 
        if ($perPage === 'Tampilkan semua') {
            //  Ambil semua data, tidak dipaginate
            $reports = $query->get();
        } else {
            //  Paginate normal
            $reports = $query->paginate((int) $perPage)->appends($request->query());
        }

        return view('reports.index', compact(
            'reports', 'start', 'end', 'reportDesignId', 'reportDesigns'
        ));
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
        $reportDesign = ReportDesign::findOrFail($request->report_design_id);
        
        // Validate main fields
        $this->validateReportData($request, $reportDesign);

        try {
            DB::beginTransaction();

            $data = $request->main_data ?? [];

            // Cek apakah ada file di main_data
            if ($request->hasFile('main_data')) {
                foreach ($request->file('main_data') as $key => $file) {
                    if ($file && $file->isValid()) {
                        // Tentukan nama file unik
                        $filename = time() . '_' . $key . '.' . $file->getClientOriginalExtension();

                        // Simpan ke folder public/assets/uploads/reports
                        $file->move(public_path('assets/uploads/reports'), $filename);

                        // Simpan path relatif di database
                        $data[$key] = 'assets/uploads/reports/' . $filename;
                    }
                }
            }

            // Create main report
            $report = Report::create([
                'report_design_id' => $reportDesign->id,
                'title' => $request->title,
                'data' => $data,
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
        try {
            DB::beginTransaction();

            // Ambil data lama (untuk fallback)
            $oldData = $report->data ?? [];

            $data = [];

            /**
             * ======================================
             * 1. PROSES MAIN DATA
             * ======================================
             */
            if ($request->has('main_data')) {
                foreach ($request->main_data as $key => $value) {

                    // Jika field ini berupa file upload
                    if ($request->hasFile("main_data.$key")) {

                        $file = $request->file("main_data.$key");

                        if ($file && $file->isValid()) {
                            $filename = time() . "_{$key}." . $file->getClientOriginalExtension();
                            $file->move(public_path('assets/uploads/reports'), $filename);

                            $data[$key] = 'assets/uploads/reports/' . $filename;
                        }

                    }
                    // Jika tidak upload file → gunakan old value
                    else if ($request->has("main_data_{$key}_old")) {

                        $data[$key] = $request->input("main_data_{$key}_old"); 
                    }
                    // Jika bukan file → simpan value baru (text, number, signature, map, dll)
                    else {
                        $data[$key] = $value;
                    }
                }
            }

            // Jika ada field lama yang tidak dikirim (karena form tidak render)
            // Tambahkan fallback agar tidak hilang
            foreach ($oldData as $key => $val) {
                if (!array_key_exists($key, $data)) {
                    $data[$key] = $val;
                }
            }


            /**
             * ======================================
             * 2. UPDATE MAIN REPORT
             * ======================================
             */
            $report->update([
                'title'  => $request->title,
                'data'   => $data,
                'status' => $request->action ?? 'draft',
            ]);


            /**
             * ======================================
             * 3. PROSES SUB DATA
             * ======================================
             */
            $report->subData()->delete();

            if ($request->has('sub_data')) {
                $this->storeSubReportData($report, $request->sub_data);
            }

            DB::commit();

            return redirect()
                ->route('reports.show', $report)
                ->with('success', 'Report berhasil diupdate!');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
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

                $data = [];

                foreach ($rowData as $key => $value) {
                    // Jika field adalah file upload
                    if ($value instanceof \Illuminate\Http\UploadedFile) {
                        if ($value->isValid()) {
                            $filename = time() . "_{$rowIndex}_{$key}." . $value->getClientOriginalExtension();

                            // simpan ke public/assets/uploads/reports
                            $value->move(public_path('assets/uploads/reports'), $filename);

                            $data[$key] = "assets/uploads/reports/" . $filename;
                        }
                    } else {
                        // Simpan field biasa
                        $data[$key] = $value;
                    }
                }

                // Simpan sub data ke DB
                ReportSubData::create([
                    'report_id'            => $report->id,
                    'report_sub_design_id' => $subDesignId,
                    'data'                 => $data,
                    'row_index'            => $rowIndex,
                ]);
            }
        }
    } 

    private function getFilteredReports(Request $request)
    {
        $status = $request->input('status');
        $start  = $request->input('date_from');
        $end    = $request->input('date_to');
        $design = $request->input('report_design_id');
        $search = $request->input('search');

        $query = Report::with(['reportDesign', 'creator', 'subData'])
                    ->latest();

        if ($start) {
            $query->whereDate('created_at', '>=', $start);
        }

        if ($end) {
            $query->whereDate('created_at', '<=', $end);
        }

        if ($design) {
            $query->where('report_design_id', $design);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                ->orWhereHas('creator', function($q2) use ($search) {
                    $q2->where('name', 'like', "%$search%");
                })
                ->orWhereHas('reportDesign', function($q2) use ($search) {
                    $q2->where('name', 'like', "%$search%");
                });
            });
        }

        return $query->get(); //  untuk export, tidak paginate
    }

    public function exportList(Request $request)
    {
        $reports = $this->getFilteredReports($request);

        if ($reports->isEmpty()) {
            return back()->with('error', 'Tidak ada data untuk diexport');
        }

        $timestamp = now()->format('Y-m-d H.i');
        $username  = auth()->user()->name;

        $fileName = "Rekap Report {$timestamp} - Download {$username}.xlsx";

        return Excel::download(new ReportListExport($reports), $fileName);
    }


    public function exportPdf(Report $report)
    {
        $pdf = \PDF::loadView('exports.report-pdf', [
            'report' => $report
        ])->setPaper('a4', 'portrait');

        return $pdf->download('report-'.$report->id.'.pdf');
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