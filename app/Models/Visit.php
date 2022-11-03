<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $dates = ['created_at', 'updated_at', 'date'];
    public $timestamps = true;

    public $fillable = ['car', 'date', 'fk_property_id'];
}
