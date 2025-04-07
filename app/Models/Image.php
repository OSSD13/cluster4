<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// @author : Pakkapon Chomchoey 66160080
class Image extends Model
{
    //
    use SoftDeletes;
    use HasFactory;
    protected $table = 'image';
    protected $primaryKey = 'i_id';
    public $timestamps = true;

    protected $fillable = [
        'i_pathUrl',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'i_br_id', 'br_id');
    }

    public function interestLocation()
    {
        return $this->belongsTo(InterestLocation::class, 'i_il_id', 'il_id');
    }
}
