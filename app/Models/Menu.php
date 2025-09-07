<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Menu extends Model
{
    use LogsActivity;

    protected $fillable = [
        'title', 'icon', 'route', 'url', 'permission', 'parent_id', 'order'
    ];

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'icon', 'route', 'url', 'permission', 'parent_id', 'order'])
            ->useLogName('menu')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
