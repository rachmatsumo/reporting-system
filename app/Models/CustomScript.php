<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomScript extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'script', 'is_active', 'user_id'];
}
