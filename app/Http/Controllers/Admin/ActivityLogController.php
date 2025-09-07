<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index()
    {
        // Ambil activity log terbaru, 10 per halaman
        $activities = ActivityLog::with('causer')->latest()->paginate(10);

        return view('admin.activity_log.index', compact('activities'));
    }

}
