<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Get one item from database according to $modelClass inherited from Illuminate\Database\Eloquent\Model
     * 
     * @param Illuminate\Database\Eloquent\Collection $modelClass
     * @param int $id
     * @return Illuminate\Database\Eloquent\Model
     */
    protected function getItem($modelClass, $id, $enabledValueOnly = false)
    {
        if($id < 1) {
            return $this->errorMessage("ID must be greater than 0.", 400);
        } 

        $itemQuery = $modelClass::where('id', $id);
        if ($enabledValueOnly)
            $itemQuery = $itemQuery->where('enable', $enabledValueOnly);

        return $itemQuery->first();
    }

    /**
     * Return the paginated items according to $modelClass inherited from Illuminate\Database\Eloquent\Model
     */
    protected function doPagination($modelClass, Request $request)
    {
        $otherRequests = $request->except('page');

        $pageSize = $request->has('size') ? $request->size : 10;
        if($pageSize < 1) {
            return $this->errorMessage("Page size must be greater than 0.", 400);
        } 

        $items = $modelClass::latest()->paginate($pageSize);
        foreach($otherRequests as $key => $otherRequest) {
            $items->appends([$key => $otherRequest]);
        }

        return $items;
    }

    /**
     * Do the setting of "enable" field in DB according to $enableValue
     * 
     * @param Illuminate\Database\Eloquent\Model $model
     * @param bool $enableValue
     * @return \Illuminate\Http\Response
     */
    protected function changeEnableValue($model, bool $enableValue, $itemType)
    {
        try {
            DB::beginTransaction();  

            $model->enable = $enableValue;
            $model->save();

            DB::commit();

            return [         
                'message' => $itemType.' '.$model->id.' has been '. ($enableValue ? 'enabled.' : 'disabled'),
                'status' => 1
            ];
        } catch (\Throwable $th) {
            $this->WriteLog(
                'Failed to '.($enableValue ? 'enable.' : 'disable').' the '.$itemType.' because an exception has occurred.',
                ['id' => $model->id, 'exception' => $th->getMessage()],
                'critical'
            );
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Outputs the success message to client
     */
    protected function successMessage(string $message, $data, $status = 200)
    {
        return [
            "message" => $message,
            "status" => $status,
            "data" => $data,
        ];
    }

    /**
     * Outputs the error message to client
     */
    protected function errorMessage(string $message, int $status, $details = null)
    {
        $jsonData = [
            'message' => $message,
            "status" => $status,
        ];
        if(isset($details))
            $jsonData['details'] = $details;

        return response()->json($jsonData, $status);
    }




    /**
     * Write a log record
     * 
     * @param string $message
     */
    protected function WriteLog($message, $context = [], $level = 'error')
    {
        $actionName = Route::getCurrentRoute()->getActionName();

        switch ($level) {
            case 'error':
                Log::error(
                    "[$actionName] $message", 
                    $context
                );
                break;
            case 'critical':
                Log::critical(
                    "[$actionName] $message", 
                    $context
                );
                break;
            case 'warning':
                Log::warning(
                    "[$actionName] $message", 
                    $context
                );
                break;
            
            default:
                Log::error(
                    "[$actionName] $message", 
                    $context
                );
                break;
        }
               
    }

    /**
     * Validate the incoming request
     * 
     * @param \Illuminate\Http\Request $request
     * @param array $rules
     * @return array $errorMsg
     */
    protected function validateRequest(Request $request, $rules, $checkFileUploadField = false)
    {
        $errorMsg = [];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $this->WriteLog(
                'Request validation failed because the incoming request failed to satisfy the rules.',
                $validator->errors()->toArray(),
                'error'
            );
            $errorMsg = [
                'error' => 'BadRequest',
                'validationErrorMessages' => $validator->errors()
            ];
        } 

        if($checkFileUploadField) {
            if(!$request->hasFile('file')) {
                $this->WriteLog(
                    'Request validation failed because the incoming request has no file field for file uploading.',
                    [],
                    'error'
                );

                $errorMsg = [
                    'error' => 'BadRequest',
                    'validationErrorMessages' => 'No files to upload in the request'
                ];
            }
        }

        return $errorMsg;
    }

    /**
     * Outputs the item not found error message to client
     */
    protected function itemNotFound($itemType, $id)
    {
        return $this->errorMessage("$itemType $id not found in the database", 404);
    }
}
