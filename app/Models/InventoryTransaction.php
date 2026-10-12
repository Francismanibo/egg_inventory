<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class InventoryTransaction extends Model { protected $fillable=['product_id','production_id','transaction_type','quantity','reason','transaction_date','recorded_by','remarks']; protected $casts=['transaction_date'=>'date','quantity'=>'integer']; public function product(){return $this->belongsTo(EggProduct::class,'product_id');} public function production(){return $this->belongsTo(ProductionRecord::class,'production_id');} public function recorder(){return $this->belongsTo(User::class,'recorded_by');} }
