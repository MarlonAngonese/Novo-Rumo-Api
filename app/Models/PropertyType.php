<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class PropertyType extends Model
{
    use HasFactory;

    protected $dates = ['created_at', 'updated_at'];
    public $timestamps = true;
}
