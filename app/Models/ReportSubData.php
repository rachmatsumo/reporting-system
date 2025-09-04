<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ReportSubData extends Model
{
    protected $fillable = [
        'report_id',
        'report_sub_design_id',
        'data',
        'row_index'
    ];

    protected $casts = [
        'data' => 'array',
        'row_index' => 'integer'
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function reportSubDesign(): BelongsTo
    {
        return $this->belongsTo(ReportSubDesign::class);
    }
}
