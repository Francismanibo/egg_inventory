<?php
namespace App\Services;
use App\Models\EggProduct; use App\Models\InventoryTransaction;
class InventoryService {
 public function balance(int $productId):int{return (int)InventoryTransaction::where('product_id',$productId)->get(['transaction_type','quantity'])->sum(fn($t)=>match($t->transaction_type){'addition'=>$t->quantity,'deduction'=>-$t->quantity,'adjustment'=>$t->quantity,default=>0});}
 public function allBalances(){return EggProduct::where('status','active')->orderBy('product_name')->get()->map(function($p){$p->current_stock=$this->balance($p->id);$p->low_stock=$p->current_stock<=$p->reorder_level;return $p;});}
}
