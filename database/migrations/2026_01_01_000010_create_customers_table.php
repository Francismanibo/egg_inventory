<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void{Schema::create('customers',function(Blueprint $t){$t->id();$t->string('name',100);$t->string('contact',30);$t->string('email',150)->nullable();$t->string('address',255)->nullable();$t->timestamps();$t->index('contact');});} public function down():void{Schema::dropIfExists('customers');} };
