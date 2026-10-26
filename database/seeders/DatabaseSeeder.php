<?php

namespace Database\Seeders;

use App\Models\EggProduct;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminPassword = (string) env('EGG_ADMIN_PASSWORD', '');
        $userPassword = (string) env('EGG_USER_PASSWORD', '');

        if (strlen($adminPassword) < 12 || strlen($userPassword) < 12) {
            throw new RuntimeException(
                'Set EGG_ADMIN_PASSWORD and EGG_USER_PASSWORD in .env to unique passwords of at least 12 characters before seeding.'
            );
        }

        if (hash_equals($adminPassword, $userPassword)) {
            throw new RuntimeException('Use different passwords for the Admin and User accounts.');
        }

        User::updateOrCreate(
            ['email' => 'admin@egg.local'],
            [
                'name' => 'Administrator',
                'password' => $adminPassword,
                'role' => 'admin',
                'status' => 'active',
            ],
        );

        User::updateOrCreate(
            ['email' => 'user@egg.local'],
            [
                'name' => 'System User',
                'password' => $userPassword,
                'role' => 'user',
                'status' => 'active',
            ],
        );

        foreach ([
            ['Regular Eggs (Tray)', 'Regular', 180],
            ['Large Eggs (Tray)', 'Large', 200],
            ['Jumbo Eggs (Tray)', 'Jumbo', 220],
        ] as [$name, $grade, $price]) {
            EggProduct::updateOrCreate(
                ['product_name' => $name],
                [
                    'egg_type' => 'Chicken Eggs',
                    'size_grade' => $grade,
                    'unit' => 'tray',
                    'price' => $price,
                    'reorder_level' => 20,
                    'status' => 'active',
                ],
            );
        }
    }
}
