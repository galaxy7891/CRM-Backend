<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResponseResource;
use App\Models\ActivityLog;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @param ActivityLogService $activityLogService
     */
    public function index(ActivityLogService $activityLogService)
    {
        try {
            $result = $activityLogService->getFormattedLogs();
    
            return new ApiResponseResource(
                true,
                'Daftar aktivitas',
                $result
            );
        }

        catch (\Exception $e){
            return new ApiResponseResource(
                false,
                $e->getMessage(),
                null
            );
        }
    }

    /**
     * Display a detail listing of the resource.
     */
    public function detail(ActivityLogService $activityLogService)
    {
        try {
            $result = $activityLogService->getDetailLogs();

            return new ApiResponseResource(
                true,
                'Daftar detail aktivitas',
                $result
            );
        }

        catch (\Exception $e){
            return new ApiResponseResource(
                false,
                $e->getMessage(),
                null
            );
        }
    }
}
