<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product_Image extends Model
{
    use HasFactory;
    protected $table = 'product_images';
    protected $fillable = ['product_id', 'filename'];

    public function prod_img()
    {
        return $this->belongsTo(Product::class,'product_id','id');
    }
}
