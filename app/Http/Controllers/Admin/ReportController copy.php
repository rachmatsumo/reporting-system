<?php

namespace App\Http\Controllers\Admin;

use App\Models\Report;
use App\Models\ReportDesign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ReportController
{
    public function index()
    {
        $reports = Report::with(['reportDesign', 'creator'])->latest()->get();
        return view('reports.index', compact('reports'));
    }

    public function create()
    {
        $reportDesigns = ReportDesign::where('is_active', true)->get();
        return view('reports.create', compact('reportDesigns'));
    }

    public function createFromDesign(ReportDesign $reportDesign)
    {
        $users = User::select('id', 'name')->get();
        // dd($users);
        $reportDesign->load('fields');
        return view('reports.form', compact('reportDesign', 'users'));
    }

    public function store(Request $request)
    {
        $reportDesign = ReportDesign::findOrFail($request->report_design_id);
        $reportDesign->load('fields');

        // Validasi berdasarkan field design
        $validationRules = [
            'title' => 'nullable|string|max:255',
            'report_design_id' => 'required|exists:report_designs,id'
        ];

        foreach ($reportDesign->fields as $field) {
            $fieldName = "data.{$field->name}";
            $rules = [];

            if ($field->required) {
                $rules[] = 'required';
            } else {
                $rules[] = 'nullable';
            }

            switch ($field->type) {
                case 'number':
                    $rules[] = 'numeric';
                    break;
                case 'date':
                    $rules[] = 'date';
                    break;
                case 'time':
                    $rules[] = 'date_format:H:i';
                    break;
                case 'month':
                    $rules[] = 'date_format:Y-m';
                    break;
                case 'year':
                    $rules[] = 'date_format:Y';
                    break;
                case 'file':
                case 'image':
                    $rules[] = 'file';
                    if ($field->type === 'image') {
                        $rules[] = 'image|mimes:jpeg,png,jpg,gif|max:2048';
                    }
                    break;
                case 'checkbox':
                    $rules[] = 'boolean';
                    break;
                case 'select':
                    // Validasi bahwa value ada dalam options
                    $options = json_decode($field->default_value, true);
                    if ($options) {
                        $validValues = array_column($options, 'value');
                        $rules[] = 'in:' . implode(',', $validValues);
                    }
                    break;
                case 'personnel':
                    // Validasi personnel array
                    $rules = ['nullable', 'array'];
                    $validationRules["data.{$field->name}.*"] = 'exists:users,id';
                    break;
                case 'attendance':
                    // Validasi attendance array
                    $rules = ['nullable', 'array'];
                    $validationRules["data.{$field->name}.*.user_id"] = 'required|exists:users,id';
                    $validationRules["data.{$field->name}.*.status"] = 'required|in:Present,Absent,Leave,Permit';
                    break;
                default:
                    $rules[] = 'string|max:5000';
            }

            if ($field->type !== 'personnel' && $field->type !== 'attendance') {
                $validationRules[$fieldName] = implode('|', $rules);
            }
        }

        $request->validate($validationRules);

        // Process form data
        $data = $request->input('data', []);

        // Handle file uploads
        foreach ($reportDesign->fields as $field) {
            if (in_array($field->type, ['file', 'image']) && $request->hasFile("data.{$field->name}")) {
                $file = $request->file("data.{$field->name}");
                $path = $file->store('assets/uploads/reports/' . $reportDesign->id, 'public');
                $data[$field->name] = $path;
            }
        }

        // Handle personnel data
        foreach ($reportDesign->fields as $field) {
            if ($field->type === 'personnel') {
                $personnelData = $request->input("data.{$field->name}", []);
                // Filter empty values dan convert ke array of user IDs
                $data[$field->name] = array_values(array_filter($personnelData));
            }
        }

        // Handle attendance data
        foreach ($reportDesign->fields as $field) {
            if ($field->type === 'attendance') {
                $attendanceData = $request->input("data.{$field->name}", []);
                $processedAttendance = [];
                
                foreach ($attendanceData as $key => $attendance) {
                    // Skip jika user_id kosong
                    if (!empty($attendance['user_id'])) {
                        $processedAttendance[$key] = [
                            'user_id' => $attendance['user_id'],
                            'status' => $attendance['status'] ?? 'Present'
                        ];
                    }
                }
                
                $data[$field->name] = $processedAttendance;
            }
        }

        Report::create([
            'report_design_id' => $request->report_design_id,
            'title' => $request->title,
            'data' => $data,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('reports.index')
                        ->with('success', 'Report berhasil dibuat');
    }

    public function show(Report $report)
    {
        $users = User::select('id', 'name')->get();
        $report->load(['reportDesign.fields', 'creator']);
        return view('reports.show', compact('report', 'users'));
    }

    public function edit(Report $report)
    {
        $users = User::all();
        $report->load(['reportDesign.fields']);
        return view('reports.edit', compact('report', 'users'));
    }

    public function update(Request $request, Report $report)
    {
        $reportDesign = $report->reportDesign;
        $reportDesign->load('fields');

        // Validasi berdasarkan field design (sama seperti store)
        $validationRules = [
            'title' => 'nullable|string|max:255',
        ];

        foreach ($reportDesign->fields as $field) {
            $fieldName = "data.{$field->name}";
            $rules = [];

            if ($field->required) {
                $rules[] = 'required';
            } else {
                $rules[] = 'nullable';
            }

            switch ($field->type) {
                case 'number':
                    $rules[] = 'numeric';
                    break;
                case 'date':
                    $rules[] = 'date';
                    break;
                case 'time':
                    $rules[] = 'date_format:H:i';
                    break;
                case 'month':
                    $rules[] = 'date_format:Y-m';
                    break;
                case 'year':
                    $rules[] = 'date_format:Y';
                    break;
                case 'file':
                case 'image':
                    // Pada update, file tidak wajib
                    $rules = ['nullable', 'file'];
                    if ($field->type === 'image') {
                        $rules[] = 'image|mimes:jpeg,png,jpg,gif|max:2048';
                    }
                    break;
                case 'checkbox':
                    $rules[] = 'boolean';
                    break;
                case 'select':
                    $options = json_decode($field->default_value, true);
                    if ($options) {
                        $validValues = array_column($options, 'value');
                        $rules[] = 'in:' . implode(',', $validValues);
                    }
                    break;
                case 'personnel':
                    $rules = ['nullable', 'array'];
                    $validationRules["data.{$field->name}.*"] = 'exists:users,id';
                    break;
                case 'attendance':
                    $rules = ['nullable', 'array'];
                    $validationRules["data.{$field->name}.*.user_id"] = 'required|exists:users,id';
                    $validationRules["data.{$field->name}.*.status"] = 'required|in:Present,Absent,Leave,Permit';
                    break;
                default:
                    $rules[] = 'string|max:5000';
            }

            if ($field->type !== 'personnel' && $field->type !== 'attendance') {
                $validationRules[$fieldName] = implode('|', $rules);
            }
        }

        $request->validate($validationRules);

        // Get existing data
        $data = $report->data ?? [];

        // Update dengan data baru dari request
        $newData = $request->input('data', []);
        
        // Handle file uploads
        foreach ($reportDesign->fields as $field) {
            if (in_array($field->type, ['file', 'image'])) {
                if ($request->hasFile("data.{$field->name}")) {
                    // Delete old file if exists
                    if (!empty($data[$field->name])) {
                        Storage::disk('public')->delete($data[$field->name]);
                    }
                    
                    // Upload new file
                    $file = $request->file("data.{$field->name}");
                    $path = $file->store('assets/uploads/reports/' . $reportDesign->id, 'public');
                    $data[$field->name] = $path;
                }
                // Jika tidak ada file baru, biarkan file lama
            } else {
                // Update data biasa
                if (isset($newData[$field->name])) {
                    $data[$field->name] = $newData[$field->name];
                }
            }
        }

        // Handle personnel data
        foreach ($reportDesign->fields as $field) {
            if ($field->type === 'personnel') {
                $personnelData = $request->input("data.{$field->name}", []);
                // Filter empty values dan convert ke array of user IDs
                $data[$field->name] = array_values(array_filter($personnelData));
            }
        }

        // Handle attendance data
        foreach ($reportDesign->fields as $field) {
            if ($field->type === 'attendance') {
                $attendanceData = $request->input("data.{$field->name}", []);
                $processedAttendance = [];
                
                foreach ($attendanceData as $key => $attendance) {
                    // Skip jika user_id kosong
                    if (!empty($attendance['user_id'])) {
                        $processedAttendance[$key] = [
                            'user_id' => $attendance['user_id'],
                            'status' => $attendance['status'] ?? 'Present'
                        ];
                    }
                }
                
                $data[$field->name] = $processedAttendance;
            }
        }

        $report->update([
            'title' => $request->title,
            'data' => $data,
        ]);

        return redirect()->route('reports.show', $report)
                        ->with('success', 'Report berhasil diupdate');
    }

    // public function update(Request $request, Report $report)
    // {
    //     $reportDesign = $report->reportDesign;
    //     $reportDesign->load('fields');

    //     // Same validation as store method
    //     $validationRules = [
    //         'title' => 'required|string|max:255'
    //     ];

    //     foreach ($reportDesign->fields as $field) {
    //         $fieldName = "data.{$field->name}";
    //         $rules = [];

    //         if ($field->required) {
    //             $rules[] = 'required';
    //         } else {
    //             $rules[] = 'nullable';
    //         }

    //         switch ($field->type) {
    //             case 'number':
    //                 $rules[] = 'numeric';
    //                 break;
    //             case 'date':
    //                 $rules[] = 'date';
    //                 break;
    //             case 'file':
    //             case 'image':
    //                 $rules[] = 'file';
    //                 if ($field->type === 'image') {
    //                     $rules[] = 'image|mimes:jpeg,png,jpg,gif|max:2048';
    //                 }
    //                 break;
    //             default:
    //                 $rules[] = 'string|max:5000';
    //         }

    //         $validationRules[$fieldName] = implode('|', $rules);
    //     }

    //     $request->validate($validationRules);

    //     $data = $report->data;

    //     // Handle file uploads
    //     foreach ($reportDesign->fields as $field) {
    //         if (in_array($field->type, ['file', 'image']) && $request->hasFile("data.{$field->name}")) {
    //             // Delete old file if exists
    //             if (isset($data[$field->name]) && Storage::disk('public')->exists($data[$field->name])) {
    //                 Storage::disk('public')->delete($data[$field->name]);
    //             }

    //             $file = $request->file("data.{$field->name}");
    //             $path = $file->store('assets/uploads/reports/' . $reportDesign->id, 'public');
    //             $data[$field->name] = $path;
    //         } else if ($request->has("data.{$field->name}")) {
    //             $data[$field->name] = $request->input("data.{$field->name}");
    //         }
    //     }

    //     $report->update([
    //         'title' => $request->title,
    //         'data' => $data,
    //     ]);

    //     return redirect()->route('reports.index')
    //                     ->with('success', 'Report berhasil diupdate');
    // }

    public function destroy(Report $report)
    {
        // Delete associated files
        foreach ($report->data as $key => $value) {
            if (is_string($value) && Storage::disk('public')->exists($value)) {
                Storage::disk('public')->delete($value);
            }
        }

        $report->delete();
        
        return redirect()->route('reports.index')
                        ->with('success', 'Report berhasil dihapus');
    }
}