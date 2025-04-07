<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// @author : Pakkapon Chomchoey 66160080
class InterestLocation extends Model
{
    //
    use SoftDeletes;
    use HasFactory;
    protected $table = 'interest_location';
    protected $primaryKey = 'il_id';
    public $timestamps = true;

    protected $fillable = [
        'il_name',
        'il_scope',
        'il_longlat',
        'il_address',
        'il_subdistrict',
        'il_district',
        'il_province',
        'il_postalcode',
        'il_tl_id',
        'il_us_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'il_us_id', 'us_id');
    }

    public function image()
    {
        return $this->hasMany(Image::class, 'i_il_id', 'il_id');
    }

    public function typeLocation()
    {
        return $this->belongsTo(TypeLocation::class, 'il_tl_id', 'tl_id');
    }
}
