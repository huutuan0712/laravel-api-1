<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;
    protected $table ='district';
    protected $fillable = [
        '_name',
        '_prefix',
        '_province_id',
    ];
    public function province(){
        return $this->belongsTo(Province::class,'_province_id','id');
    }
}
