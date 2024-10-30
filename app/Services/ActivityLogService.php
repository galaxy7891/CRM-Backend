<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Helpers\ActionMapperHelper;
use Illuminate\Support\Facades\Log;

class ActivityLogService
{
    public function getDetailLogs()
    {
        $hiddenProperties = [
            'id', 'password', 'company_id', 'google_id', 'organization_id', 'customer_id', 'deals_id', 'product_id', 'image_public_id', 'token', 'expired_at', 'created_at', 'updated_at', 'deleted_at'
        ];

        $paginatedLogs = ActivityLog::getPaginatedLogs();

        $result = $paginatedLogs->getCollection()->map(function ($log) use ($hiddenProperties){
            $changes = json_decode($log->changes, true);
            $actionTitle = ActionMapperHelper::mapActionTitle($log->action);
            $modelName = ActionMapperHelper::mapModels($log->model_name);

            $properties = [];
            $before = [];
            $after = [];

            if (is_array($changes)) {
                foreach ($changes as $key => $value) {
                    if (!in_array($key, $hiddenProperties)) {
                        $mappedPropertyName = ActionMapperHelper::mapProperties($key, $log->model_name);
                        $properties[] = ucfirst($mappedPropertyName);
            
                        $before[] = array_key_exists('old', $value) ? $value['old'] : null;
                        $after[] = array_key_exists('new', $value) ? $value['new'] : null;
                    }
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
    
    public function getFormattedLogsTest(string $modelName = null, string $id = null)
    {
        $groupedLogsData = ActivityLog::getLogsGroupedByMonthForModel($modelName, $id);
        $paginatedLogs = $groupedLogsData['paginatedLogs'];
        $logs = $groupedLogsData['logs'];

        $result = [];
        foreach ($logs as $month => $logGroup) {
            $activities = $logGroup->map(function ($log) use ($modelName) {
                $changes = json_decode($log->changes, true);
                $modelToUse = ($modelName === 'users' ? $log->model_name : $modelName);
                if ($modelToUse === 'customers') {
                    $modelToUse = $changes['customerCategory']['new'] ?? $changes['customerCategory']['old'];
                }

                $description = ActionMapperHelper::mapDescription($log, $changes, $modelToUse);
                $actionTitle = ActionMapperHelper::mapActionTitle($log->action);
                $modelTitle = ActionMapperHelper::mapModels($modelToUse);

                return [
                    'title' => $actionTitle . " Data - " . ucfirst($modelTitle),
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
}