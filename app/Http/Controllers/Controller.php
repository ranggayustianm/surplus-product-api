<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

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
}
