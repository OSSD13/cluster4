<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// @author : Pakkapon Chomchoey 66160080
class TypeLocation extends Model
{
    //
    use HasFactory;
    use SoftDeletes;
    protected $table = 'type_location';
    protected $primaryKey = 'tl_id';
    public $timestamps = true;

    protected $fillable = [
        'tl_name',
        'tl_emoji',
        'tl_color',
    ];

    public function interestLocation()
    {
        return $this->hasMany(InterestLocation::class, 'il_tl_id', 'tl_id');
    }
}
