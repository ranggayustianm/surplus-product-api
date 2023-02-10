<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Image;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    const PRODUCT_VALIDATION_RULES = [
        'name' => 'required',
        'description' => 'required',
        'enable' => 'required',
        'image_ids' => 'nullable|array',
        'image_ids.*' => 'numeric|integer|exists:App\Models\Image,id',
        'category_ids' => 'nullable|array',
        'category_ids.*' => 'numeric|integer|exists:App\Models\Category,id',
    ];
    const SET_IMAGE_VALIDATION_RULES = [
        'image_ids' => 'required|array',
        'image_ids.*' => 'numeric|integer|exists:App\Models\Image,id',
    ];
    const SET_CATEGORY_VALIDATION_RULES = [
        'category_ids' => 'required|array',
        'category_ids.*' => 'numeric|integer|exists:App\Models\Category,id',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->doPagination(Product::class, $request);
    }

    /**
     * Set "enable" field to true/false.
     *
     * @param  int $id
     * @param  bool $enableValue
     * @return \Illuminate\Http\Response
     */
    public function setEnable($id, $enableValue)
    {
        if($enableValue !== "enable" && $enableValue !== "disable"){
            return $this->errorMessage("Invalid request", 400);
        }          

        $product = $this->getItem(Product::class, $id);
        if(!($product)) {
            return $this->itemNotFound('Product', $id);
        }

        return $this->changeEnableValue($product, ($enableValue === 'enable'), 'Product');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validationErrors = $this->validateRequest($request, self::PRODUCT_VALIDATION_RULES);
        if(!empty($validationErrors)) {
            return $this->errorMessage("Request validation failed", 400, $validationErrors);
        }

        try {
            DB::beginTransaction();
            $product = Product::create($request->except(['image_ids', 'category_ids']));

            if($request->has('image_ids')) {
                $product->images()->attach(
                    $request->image_ids, 
                    ['created_at' => new DateTime(), 'updated_at' => new DateTime()]
                );
            }
            if($request->has('category_ids')) {
                $product->categories()->attach(
                    $request->category_ids, 
                    ['created_at' => new DateTime(), 'updated_at' => new DateTime()]
                );
            }

            DB::commit();
            return $this->successMessage("New product has been created.", $product);
        } catch (\Throwable $th) {
            $this->WriteLog(
                'Failed to create the product because an exception has occurred.',
                ['exception' => $th->getMessage()],
                'critical'
            );
            DB::rollBack();
            throw $th;
        }  
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = $this->getItem(Product::class, $id);
        if(!($product)) {
            return $this->itemNotFound('Product', $id);
        }

        return $product;
    }

    /**
     * Display the the images of a product
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function showImages($id)
    {
        $product = $this->getItem(Product::class, $id);
        if(!($product)) {
            return $this->itemNotFound('Product', $id);
        }

        $images = $product->images()->get();
        if($images->isEmpty()) {
            return $this->errorMessage("No images in $product->name product.", 404);
        }

        return $images;
    }

    /**
     * Display the the categories of a product
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function showCategories($id)
    {
        $product = $this->getItem(Product::class, $id);
        if(!($product)) {
            return $this->itemNotFound('Product', $id);
        }

        $categories = $product->categories()->get();
        if($categories->isEmpty()) {
            return $this->errorMessage("No categories in $product->name product.", 404);
        }

        return $categories;
    }

    /**
     * Set valid image IDs to a product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function setImages(Request $request, $id)
    {
        $product = $this->getItem(Product::class, $id);
        if(!($product)) {
            return $this->itemNotFound('Product', $id);
        }

        $validationErrors = $this->validateRequest($request, self::SET_IMAGE_VALIDATION_RULES);
        if(!empty($validationErrors)) {
            return $this->errorMessage("Request validation failed", 400, $validationErrors);
        }

        try {
            $imageIds = $request->image_ids;
            
            DB::beginTransaction();   
            $product->images()->detach();
            $product->images()->attach(
                $imageIds, 
                ['created_at' => new DateTime(), 'updated_at' => new DateTime()]
            );
            DB::commit();

            return $this->successMessage('Assigning images to product '.$id.' succeed.', $imageIds);
        } catch (\Throwable $th) {
            $this->WriteLog(
                'Failed to assign images to product '.$id.' because an exception has occurred.',
                ['exception' => $th->getMessage()],
                'critical'
            );
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Set valid category IDs to a product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function setCategories(Request $request, $id)
    {
        $product = $this->getItem(Product::class, $id);
        if(!($product)) {
            return $this->itemNotFound('Product', $id);
        }

        $validationErrors = $this->validateRequest($request, self::SET_CATEGORY_VALIDATION_RULES);
        if(!empty($validationErrors)) {
            return $this->errorMessage("Request validation failed", 400, $validationErrors);
        }
        
        try {
            $categoryIds = $request->category_ids;
            
            DB::beginTransaction();   
            $product->categories()->detach();
            $product->categories()->attach(
                $categoryIds, 
                ['created_at' => new DateTime(), 'updated_at' => new DateTime()]
            );
            DB::commit();

            return $this->successMessage('Assigning categories to product '.$id.' succeed.', $categoryIds);
        } catch (\Throwable $th) {
            $this->WriteLog(
                'Failed to assign categories to product '.$id.' because an exception has occurred.',
                ['exception' => $th->getMessage()],
                'critical'
            );
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = $this->getItem(Product::class, $id);
        if(!($product)) {
            return $this->itemNotFound('Product', $id);
        }

        $validationErrors = $this->validateRequest($request, self::PRODUCT_VALIDATION_RULES);
        if(!empty($validationErrors)) {
            return $this->errorMessage("Request validation failed", 400, $validationErrors);
        }

        try {
            DB::beginTransaction();
            $product->update($request->all());
            DB::commit();
        } catch (\Throwable $th) {
            $this->WriteLog(
                'Failed to update the product because an exception has occurred.',
                ['id' => $id, 'exception' => $th->getMessage()],
                'critical'
            );
            DB::rollBack();
            throw $th;
        }

        return $this->successMessage("Product updated successfully", $product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = $this->getItem(Product::class, $id);
        if(!($product)) {
            return $this->itemNotFound('Product', $id);
        }
        
        try {
            DB::beginTransaction();

            $product->images()->detach();
            $product->categories()->detach();
            $product->delete();
            DB::commit();
        } catch (\Throwable $th) {
            $this->WriteLog(
                'Failed to delete the product because an exception has occurred.',
                ['id' => $id, 'exception' => $th->getMessage()],
                'critical'
            );
            DB::rollBack();
            throw $th;
        }
     
        return $this->successMessage("Product deleted successfully", $product);
    }
}
