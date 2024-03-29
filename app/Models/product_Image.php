<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product_Image extends Model
{
    use HasFactory;
    protected $table = 'images';
    protected $fillable = ['prod_id', 'filename'];

    public function images()
    {
        return $this->belongsTo(Product::class,'prod_id','id');
    }
}
