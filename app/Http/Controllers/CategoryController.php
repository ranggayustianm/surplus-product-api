<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    const CATEGORY_VALIDATION_RULES = [
        'name' => 'required',
        'enable' => 'required',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->doPagination(Category::class, $request);
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

        $image = $this->getItem(Category::class, $id);
        if(!($image)) {
            return $this->itemNotFound('Category', $id);
        }

        return $this->changeEnableValue($image, ($enableValue === 'enable'), 'Category');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validationErrors = $this->validateRequest($request, self::CATEGORY_VALIDATION_RULES);
        if(!empty($validationErrors)) {
            return $this->errorMessage("Request validation failed", 400, $validationErrors);
        }

        try {
            DB::beginTransaction();
            $category = Category::create($request->all());
            DB::commit();

            return $this->successMessage("New category has been created.", $category);
        } catch (\Throwable $th) {
            $this->WriteLog(
                'Failed to create the category because an exception has occurred.',
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
        $category = $this->getItem(Category::class, $id);
        if(!($category)) {
            return $this->itemNotFound('Category', $id);
        }

        return $category;
    }

    /**
     * Display the the products within a category.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function showProducts($id)
    {
        $category = $this->getItem(Category::class, $id);
        if(!($category)) {
            return $this->itemNotFound('Category', $id);
        }

        $products = $category->products()->get();
        if($products->isEmpty()) {
            return $this->errorMessage("No products in $category->name category.", 404);
        }

        return $products;
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
        $category = $this->getItem(Category::class, $id);
        if(!($category)) {
            return $this->itemNotFound('Category', $id);
        }

        $validationErrors = $this->validateRequest($request, self::CATEGORY_VALIDATION_RULES);
        if(!empty($validationErrors)) {
            return $this->errorMessage("Request validation failed", 400, $validationErrors);
        }

        try {
            DB::beginTransaction();
            $category->update($request->all());
            DB::commit();
        } catch (\Throwable $th) {
            $this->WriteLog(
                'Failed to update the category because an exception has occurred.',
                ['id' => $id, 'exception' => $th->getMessage()],
                'critical'
            );
            DB::rollBack();
            throw $th;
        }

        return $this->successMessage("Category updated successfully", $category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = $this->getItem(Category::class, $id);
        if(!($category)) {
            return $this->itemNotFound('Category', $id);
        }

        try {
            DB::beginTransaction();
            $category->products()->detach();
            $category->delete();
            DB::commit();
        } catch (\Throwable $th) {
            $this->WriteLog(
                'Failed to delete the category because an exception has occurred.',
                ['id' => $id, 'exception' => $th->getMessage()],
                'critical'
            );
            DB::rollBack();
            throw $th;
        }
     
        return $this->successMessage("Category deleted successfully", $category);
    }
}
