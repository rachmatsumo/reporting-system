<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportSubDesign extends Model
{
    protected $fillable = [
        'report_design_id',
        'name',
        'description',
        'type',
        'order_index',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order_index' => 'integer'
    ];

    public function reportDesign(): BelongsTo
    {
        return $this->belongsTo(ReportDesign::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(ReportSubField::class)->orderBy('order_index');
    }

    public function subData(): HasMany
    {
        return $this->hasMany(ReportSubData::class);
    }

    public function subReports() {
        return $this->hasMany(ReportSubData::class, 'report_sub_design_id');
    }
}