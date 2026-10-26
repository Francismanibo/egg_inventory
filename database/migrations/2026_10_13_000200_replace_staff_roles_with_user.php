<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Old operational roles are consolidated into the single User role.
        // Disable them during migration so old, publicly documented passwords
        // cannot be used until an administrator reviews and reactivates them.
        DB::table('users')
            ->whereIn('role', ['production', 'inventory'])
            ->update([
                'role' => 'user',
                'status' => 'inactive',
            ]);

        Schema::table('users', function (Blueprint $table): void {
            $table->string('role', 50)->default('user')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role', 50)->default('inventory')->change();
        });

        DB::table('users')
            ->where('role', 'user')
            ->update(['role' => 'inventory']);
    }
};
