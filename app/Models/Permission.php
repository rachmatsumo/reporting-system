<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Permission extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'guard_name', 
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role')->withTimestamps();
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'guard_name'])
            ->useLogName('permission')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
