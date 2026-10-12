<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class EggProduct extends Model { protected $table='egg_products'; protected $fillable=['product_name','egg_type','size_grade','unit','price','reorder_level','status']; protected $casts=['price'=>'decimal:2']; public function productionRecords(){return $this->hasMany(ProductionRecord::class,'product_id');} public function inventoryTransactions(){return $this->hasMany(InventoryTransaction::class,'product_id');} public function orderItems(){return $this->hasMany(OrderItem::class,'product_id');} }
