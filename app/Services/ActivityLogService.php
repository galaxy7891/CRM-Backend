<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Helpers\ActionMapperHelper;
use App\Http\Resources\ApiResponseResource;

class ActivityLogService
{
    public function getFormattedLogs()
    {
        $groupedLogsData = ActivityLog::getLogsGroupedByMonth();    
        $paginatedLogs = $groupedLogsData['paginatedLogs'];
        $logs = $groupedLogsData['logs'];

        $result = [];
        foreach ($logs as $month => $logGroup) {
            $activities = $logGroup->map(function ($log) {
                $changes = json_decode($log->changes, true);
                $actionTitle = ActionMapperHelper::mapActionTitle($log->action);
                $actionDesc = ActionMapperHelper::mapActionDesc($log->action);

                return [
                    'title' => "{$actionTitle} Data - Halaman " . ucfirst($log->model_name),
                    'description' => "{$log->user->first_name} {$log->user->last_name} {$actionDesc} data {$log->model_name} {$changes['name']['new']}",
                    'datetime' => $log->updated_at->format('Y-m-d H:i:s'),
                ];
            });

            $result[] = [
                'month' => $month,
                'activities' => $activities,
            ];
        }

        return [
            array_merge(
                $paginatedLogs->toArray(), 
                ['data' => $result]
            )
        ];
    }
}
