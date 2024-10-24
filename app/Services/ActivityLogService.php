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

                $nameChange = $changes['name']['new'] 
                    ?? trim(($changes['first_name']['new'] ?? '') . ' ' . ($changes['last_name']['new'] ?? '')) 
                    ?: null;
                $description = "{$log->user->first_name} {$log->user->last_name} {$actionDesc} data {$modelName}";
                if ($nameChange) {
                    $description .= " {$nameChange}";
                }

                return [
                    'title' => "{$actionTitle} Data - Halaman " . ucfirst($modelName),
                    'description' => $description,
                    'datetime' => $log->updated_at->format('d-m-Y H:i:s'),
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

    public function getDetailLogs()
    {
        $hiddenProperties = [
            'id', 'password', 'company_id', 'google_id', 'organization_id', 'customer_id', 'deals_id', 'product_id', 'image_public_id', 'token', 'expired_at', 'created_at', 'updated_at', 'deleted_at'
        ];

        $paginatedLogs = ActivityLog::getPaginatedLogs();
        
        $result = $paginatedLogs->map(function ($log) use ($hiddenProperties)  {
            $changes = json_decode($log->changes, true);
            $actionTitle = ActionMapperHelper::mapActionTitle($log->action);
            $modelName = ActionMapperHelper::mapModels($log->model_name);

            $properties = [];
            $before = [];
            $after = [];
            
            foreach ($changes as $key => $value) {
                if (!in_array($key, $hiddenProperties)) {
                    $mappedPropertyName = ActionMapperHelper::mapProperties($key, $log->model_name);
                    $properties[] = ucfirst($mappedPropertyName);
                    $before[] = $value['old'] ?? null;
                    $after[] = $value['new'] ?? null; 
                }
            }

            return [
                'activities' => "{$actionTitle} " . ucfirst($modelName),
                'datetime' => $log->updated_at->format('d-m-Y H:i:s'),
                'owner' => "{$log->user->email}",
                'properties' => $properties,
                'before' => $before,
                'after' => $after
            ];
        });

        return array_merge(
            $paginatedLogs->toArray(), 
            ['data' => $result]
        );
    }

}
