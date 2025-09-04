<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_design_id',
        'title',
        'data',
        'status',
        'created_by'
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function reportDesign()
    {
        return $this->belongsTo(ReportDesign::class);
    }

    public function subData(): HasMany
    {
        return $this->hasMany(ReportSubData::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getGroupedSubData()
    {
        return $this->subData()
                   ->with('reportSubDesign')
                   ->get()
                   ->groupBy('report_sub_design_id');
    }
}