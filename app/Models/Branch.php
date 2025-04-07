<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// @author : Pakkapon Chomchoey 66160080
class Branch extends Model
{
    //
    use SoftDeletes;
    use HasFactory;
    protected $table = 'branch';
    protected $primaryKey = 'br_id';
    public $timestamps = true;
    protected $fillable = [
        'br_code',
        'br_name',
        'br_phone',
        'br_scope',
        'br_longlat',
        'br_address',
        'br_subdistrict',
        'br_district',
        'br_province',
        'br_postalcode',
        'br_us_id',
    ];

    public function manager()
    {
        return $this->belongsTo(User::class, 'br_us_id', 'us_id');
    }

    public function image()
    {
        return $this->hasMany(Image::class, 'i_br_id', 'br_id');
    }

    public function order()
    {
        return $this->hasMany(Order::class, 'od_br_id', 'br_id');
    }
}
