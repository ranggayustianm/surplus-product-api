<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;

class ProductImageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = Product::all();
        $images = Image::all();

        foreach ($products as $product) {
            $image_ids = $images->random(rand(1, count($images)))->pluck('id')->toArray();
            foreach ($image_ids as $image_id) {
                DB::table('product_image')->insert([
                    'product_id' => $product->id,
                    'image_id' => $image_id,
                    'created_at' => new DateTime(),
                    'updated_at' => new DateTime(),
                ]);
            }
        }
    }
}
