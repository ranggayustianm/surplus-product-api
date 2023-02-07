<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //use HasFactory;
    protected $table = 'products';

    protected $fillable = [
        'name',
        'description',
        'enable',
    ];

    public $timestamps = true;

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }
}
