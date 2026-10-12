<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProductionRecord extends Model { protected $fillable=['product_id','production_date','quantity_produced','saleable_quantity','damaged_quantity','rejected_quantity','recorded_by','notes']; protected $casts=['production_date'=>'date']; public function product(){return $this->belongsTo(EggProduct::class,'product_id');} public function recorder(){return $this->belongsTo(User::class,'recorded_by');} public function inventoryTransactions(){return $this->hasMany(InventoryTransaction::class,'production_id');} }
