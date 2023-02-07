<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;

class CategoryProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = Product::all();
        $categories = Category::all();

        foreach ($products as $product) {
            $category_ids = $categories->random(rand(1, count($categories)))->pluck('id')->toArray();
            foreach ($category_ids as $category_id) {
                DB::table('category_product')->insert([
                    'product_id' => $product->id,
                    'category_id' => $category_id,
                    'created_at' => new DateTime(),
                    'updated_at' => new DateTime(),
                ]);
            }
        }
    }
}
