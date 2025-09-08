<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportSubField extends Model
{
    protected $fillable = [
        'report_sub_design_id',
        'name',
        'label',
        'type',
        'required',
        'default_value',
        'options',
        'order_index'
    ];

    protected $casts = [
        'required' => 'boolean',
        'options' => 'array',
        'order_index' => 'integer'
    ];

    public function reportSubDesign(): BelongsTo
    {
        return $this->belongsTo(ReportSubDesign::class);
    }
    
}