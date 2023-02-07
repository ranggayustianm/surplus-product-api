<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->insert([
            ['name' => 'Product 1', 'description' => 'Description for product 1', 'enable' => true, 'created_at' => new DateTime(), 'updated_at' => new DateTime()],
            ['name' => 'Product 2', 'description' => 'Description for product 2', 'enable' => true, 'created_at' => new DateTime(), 'updated_at' => new DateTime()],
            ['name' => 'Product 3', 'description' => 'Description for product 3', 'enable' => true, 'created_at' => new DateTime(), 'updated_at' => new DateTime()],
            ['name' => 'Product 4', 'description' => 'Description for product 4', 'enable' => true, 'created_at' => new DateTime(), 'updated_at' => new DateTime()],
        ]);
    }
}
