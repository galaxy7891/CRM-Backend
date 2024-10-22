<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Helpers\ActionMapperHelper;

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
                $modelName = ActionMapperHelper::mapModels($log->model_name);

                $nameChange = $changes['name']['new'] ?? $changes['first_name']['new'] ?? null;
                $description = "{$log->user->first_name} {$log->user->last_name} {$actionDesc} data {$modelName}";
                if ($nameChange) {
                    $description .= " {$nameChange}";
                }

                return [
                    'title' => "{$actionTitle} Data - Halaman " . ucfirst($modelName),
                    'description' => $description,
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
