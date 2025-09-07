<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    /** @var mixed */
    public $model;
    public $actions;
    public $show;
    public $canShow;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'gender',
        'photo',
        'address',
        'phone',
        'is_active',
        'remember_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Configure activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'is_active', 'gender', 'photo', 'address', 'phone'])
            ->useLogName('user')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Prefix permission untuk user
     */
    public function permissionPrefix(): string
    {
        return 'user';
    }

    /**
     * Accessor untuk label gender
     */
    protected function genderLabel(): Attribute
    {
        return Attribute::get(function () {
            return match ($this->gender) {
                'male'   => 'Laki-laki',
                'female' => 'Perempuan',
                default  => '-',
            };
        });
    }

    /**
     * Accessor untuk status aktif
     */
    protected function isActiveLabel(): Attribute
    {
        return Attribute::get(function () {
            return $this->is_active ? 'Aktif' : 'Tidak Aktif';
        });
    }

    /**
     * Accessor untuk URL photo
     */
    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo && file_exists(public_path($this->photo))) {
            return asset($this->photo);
        }

        return asset('assets/uploads/avatar/default.png');
    }
}
