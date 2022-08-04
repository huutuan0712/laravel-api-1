<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table ='orders';
    protected $fillable=[
        'user_id',
        'name',
        'tinh',
        'huyen',
        'xa',
        'phone',
        'total_price',
        'status',
     
    ];
    public function orderItems(){
        return $this ->hasMany(OrderItems::class);
    }
}
