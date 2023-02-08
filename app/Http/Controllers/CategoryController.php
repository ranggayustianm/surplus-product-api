<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class CategoryController extends Controller
{
    const NOT_FOUND_MESSAGE = 'Category not found';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Category::latest()->where('enable', 1)->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validationErrors = $this->validateRequest($request);
        if(!empty($validationErrors)) {
            return response()->json($validationErrors, 404);
        }

        try {
            DB::beginTransaction();
            $category = Category::create($request->all());
            DB::commit();

            return [         
                "message" => "New category has been created.",
                "status" => 1,
                "data" => $category
            ];
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
        $category = $this->getCategory($id);
        if(!($category)) {
            return response()->json(['error' => self::NOT_FOUND_MESSAGE], 404);
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
        $category = $this->getCategory($id);
        if(!($category)) {
            return response()->json(['error' => self::NOT_FOUND_MESSAGE], 404);
        }

        $products = $category->products()->get();
        if($products->isEmpty()) {
            return response()->json(['error' => "No products in $category->name category."], 404);
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
        $category = $this->getCategory($id);
        if(!($category)) {
            return response()->json(['error' => self::NOT_FOUND_MESSAGE], 404);
        }

        $validationErrors = $this->validateRequest($request);
        if(!empty($validationErrors)) {
            return response()->json($validationErrors, 404);
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

        return [
            "msg" => "Category updated successfully",
            "status" => 1,
            "data" => $category,
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = $this->getCategory($id);
        if(!($category)) {
            return response()->json(['error' => self::NOT_FOUND_MESSAGE], 404);
        }

        try {
            DB::beginTransaction();
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
     
        return [
            "msg" => "Category deleted successfully",
            "status" => 1,
            "data" => $category,
        ];
    }

    /**
     * Get one category from database
     * 
     * @param int $id
     * @return \App\Models\Category
     */
    private function getCategory($id, $isEnabledOnly = true)
    {
        $category = Category::where('id', $id)
                            ->where('enable', $isEnabledOnly)
                            ->first();

        return $category;
    }

    /**
     * Validate the incoming request
     * 
     * @param \Illuminate\Http\Request $request
     * @return array $errorMsg
     */
    private function validateRequest(Request $request)
    {
        $errorMsg = [];
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'enable' => 'required',
        ]);

        if ($validator->fails()) {
            $this->WriteLog(
                'Request validation in Category failed because the incoming request failed to satisfy the rules.',
                $validator->errors()->toArray(),
                'error'
            );
            $errorMsg = [
                'error' => 'BadRequest',
                'details' => $validator->errors()
            ];
        } 
        return $errorMsg;
    }
}
