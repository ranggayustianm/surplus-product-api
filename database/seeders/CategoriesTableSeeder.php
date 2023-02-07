<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
            ['name' => 'Fashion', 'enable' => true, 'created_at' => new DateTime(), 'updated_at' => new DateTime()],
            ['name' => 'Technology', 'enable' => true, 'created_at' => new DateTime(), 'updated_at' => new DateTime()],
            ['name' => 'Sports', 'enable' => true, 'created_at' => new DateTime(), 'updated_at' => new DateTime()],
            ['name' => 'Health', 'enable' => true, 'created_at' => new DateTime(), 'updated_at' => new DateTime()],
            ['name' => 'Entertainment', 'enable' => true, 'created_at' => new DateTime(), 'updated_at' => new DateTime()],
        ]);
    }
}
