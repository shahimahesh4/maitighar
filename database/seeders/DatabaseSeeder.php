<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Maitighar Admin', 'password' => bcrypt('password'), 'email_verified_at' => now(), 'role' => 'super_admin']
        );

        User::firstOrCreate(
            ['email' => 'customer@example.com'],
            ['name' => 'Demo Customer', 'password' => bcrypt('password'), 'email_verified_at' => now(), 'role' => 'customer']
        );

        $this->call([
            HomeSliderSeeder::class,
            MaitigharProductsSeeder::class,
            AboutUsPageSeeder::class,
            PolicyPageSeeder::class,
            GalleryDemoSeeder::class,
            VideoGallerySeeder::class,
            OperationalDemoSeeder::class,
            PaymentGatewaySeeder::class,
            SuperAdminSeeder::class,
        ]);
    }
}
