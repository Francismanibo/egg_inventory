<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('egg_products', 'image_path')) {
            Schema::table('egg_products', function (Blueprint $table): void {
                $table->string('image_path')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('egg_products', 'image_path')) {
            Schema::table('egg_products', function (Blueprint $table): void {
                $table->dropColumn('image_path');
            });
        }
    }
};
