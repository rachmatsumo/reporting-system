<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportField extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_design_id',
        'name',
        'label',
        'type',
        'required',
        'default_value',
        'options',
        'order',
    ];

    protected $casts = [
        'required' => 'boolean',
        'options' => 'array',
    ];

    public function reportDesign()
    {
        return $this->belongsTo(ReportDesign::class);
    }

    public function getSelectOptionsAttribute()
    {
        if ($this->type === 'select' && $this->default_value) {
            return json_decode($this->default_value, true);
        }
        return [];
    }
}