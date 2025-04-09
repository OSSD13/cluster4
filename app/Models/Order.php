<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// @author : Pakkapon Chomchoey 66160080
class Order extends Model
{
    //
    use SoftDeletes;
    use HasFactory;
    protected $table = 'order';
    protected $primaryKey = 'od_id';
    public $timestamps = true;

    protected $fillable = [
        'od_amount',
        'od_month',
        'od_year',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'od_br_id', 'br_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'od_us_id', 'us_id');
    }
}
