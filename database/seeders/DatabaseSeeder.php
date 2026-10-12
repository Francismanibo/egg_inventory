<?php
namespace Database\Seeders;
use App\Models\{EggProduct,User}; use Illuminate\Database\Seeder; use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder { public function run():void{
 User::updateOrCreate(['email'=>'admin@egg.local'],['name'=>'Administrator','password'=>Hash::make('password'),'role'=>'admin','status'=>'active']);
 User::updateOrCreate(['email'=>'production@egg.local'],['name'=>'Production User','password'=>Hash::make('password'),'role'=>'production','status'=>'active']);
 User::updateOrCreate(['email'=>'inventory@egg.local'],['name'=>'Inventory User','password'=>Hash::make('password'),'role'=>'inventory','status'=>'active']);
 foreach([['Regular Eggs (Tray)','Regular',180],['Large Eggs (Tray)','Large',200],['Jumbo Eggs (Tray)','Jumbo',220]] as [$name,$grade,$price]) EggProduct::updateOrCreate(['product_name'=>$name],['egg_type'=>'Chicken Eggs','size_grade'=>$grade,'unit'=>'tray','price'=>$price,'reorder_level'=>20,'status'=>'active']);
} }
