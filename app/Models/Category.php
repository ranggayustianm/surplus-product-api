<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'enable',
    ];

    public $timestamps = true;

    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product');
    }
}
