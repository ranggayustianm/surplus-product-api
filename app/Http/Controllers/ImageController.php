<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    const IMAGE_VALIDATION_RULES = [
        'name' => 'required',
        'file.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'enable' => 'required',
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->doPagination(Image::class);
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

        $image = $this->getItem(Image::class, $id);
        if(!($image)) {
            return $this->itemNotFound('Image', $id);
        }

        return $this->changeEnableValue($image, ($enableValue === 'enable'), 'Image');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validationErrors = $this->validateRequest($request, self::IMAGE_VALIDATION_RULES, true);
        if(!empty($validationErrors)) {
            return $this->errorMessage("Request validation failed", 400, $validationErrors);
        }

        try {
            $files = $request->file('file');
            $images = [];
            
            DB::beginTransaction();   
            foreach ($files as $file) {
                $fileName = time() . '-' . $file->getClientOriginalName();
   
                Storage::putFileAs('public/images', $file, $fileName);

                $images[] = Image::create([
                    'name' => $request->name,
                    'file' => $fileName,
                    'enable' => $request->enable,
                ]);
            }        
            DB::commit();

            return $this->successMessage('New image(s) has been created.', $images);
        } catch (\Throwable $th) {
            $this->WriteLog(
                'Failed to create the image because an exception has occurred.',
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
        $image = $this->getItem(Image::class, $id);
        if(!($image)) {
            return $this->itemNotFound('Image', $id);
        }

        return $image;
    }

    /**
     * Display the the products within an image.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function showProducts($id)
    {
        $image = $this->getItem(Image::class, $id);
        if(!($image)) {
            return $this->itemNotFound('Image', $id);
        }

        $products = $image->products()->get();
        if($products->isEmpty()) {
            return $this->errorMessage("No products in $image->name image.", 404);
        }

        return $products;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $image = $this->getItem(Image::class, $id);
        if(!($image)) {
            return $this->itemNotFound('Image', $id);
        }

        $validationRules = self::IMAGE_VALIDATION_RULES;
        $validationRules['file'] = $validationRules['file.*'];
        unset($validationRules['file.*']);
        $validationRules['file'] = \str_replace("required","nullable",$validationRules['file']);

        $validationErrors = $this->validateRequest($request, $validationRules, false);
        if(!empty($validationErrors)) {
            return $this->errorMessage("Request validation failed", 400, $validationErrors);
        }

        try {
            DB::beginTransaction();
            $image->name = $request->input('name');

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time().'_'.$file->getClientOriginalName();
                
                Storage::delete('public/images/'.$image->file);
   
                Storage::putFileAs('public/images', $file, $fileName);
                $image->file = $fileName;
            }

            $image->save();

            DB::commit();
            return $this->successMessage('Image has been updated.', $image);
        } catch (\Throwable $th) {
            $this->WriteLog(
                'Failed to update the image because an exception has occurred.',
                ['id' => $id, 'exception' => $th->getMessage()],
                'critical'
            );
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $image = $this->getItem(Image::class, $id);
        if(!($image)) {
            return $this->itemNotFound('Image', $id);
        }

        try {
            DB::beginTransaction();

            Storage::delete('public/images/'.$image->file);

            $image->products()->detach();
            $image->delete();
            DB::commit();
        } catch (\Throwable $th) {
            $this->WriteLog(
                'Failed to delete the image because an exception has occurred.',
                ['id' => $id, 'exception' => $th->getMessage()],
                'critical'
            );
            DB::rollBack();
            throw $th;
        }
     
        return $this->successMessage("Image deleted successfully", $image);
    }
}
