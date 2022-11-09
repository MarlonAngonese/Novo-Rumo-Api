<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $dates = ['created_at', 'updated_at'];
    public $timestamps = true;
    public $fillable = [
        'code',
        'has_geo_board',
        'qty_people',
        'has_cams',
        'has_phone_signal',
        'has_internet',
        'has_gun',
        'has_gun_local',
        'gun_local_description',
        'qty_agricultural_defensive',
        'observations',
        'latitude',
        'longitude',
        'fk_owner_id',
        'fk_property_type_id',
    ];
}
