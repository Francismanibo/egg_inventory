<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Order extends Model { protected $fillable=['customer_id','order_number','order_date','estimated_total','status','notes']; protected $casts=['order_date'=>'datetime','estimated_total'=>'decimal:2']; public function customer(){return $this->belongsTo(Customer::class);} public function items(){return $this->hasMany(OrderItem::class);} }
