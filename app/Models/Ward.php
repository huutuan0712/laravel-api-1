<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ward extends Model
{
    use HasFactory;
    protected $table ='ward';
    protected $fillable = [
        '_name',
        '_prefix',
        '_province_id',
        '_district_id'
    ];
    public function district(){
        return $this->belongsTo(District::class,'_district_id','id');
    }
  
}
