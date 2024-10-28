<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Helpers\ActionMapperHelper;

class ActivityLogService
{
    public function getFormattedLogs(string $modelName = null)
    {
        $groupedLogsData = ActivityLog::getLogsGroupedByMonth();
        $paginatedLogs = $groupedLogsData['paginatedLogs'];
        $logs = $groupedLogsData['logs'];
        return ActivityLog::getLogsGroupedByMonthForModel($modelName);

        $result = [];
        foreach ($logs as $month => $logGroup) {
            $activities = $logGroup->map(function ($log) {
                $changes = json_decode($log->changes, true);
                $isSelfUpdate = $log->user->id === ($changes['id']['new'] ?? null);

                $actionTitle = ActionMapperHelper::mapActionTitle($log->action);
                $actionDesc = ActionMapperHelper::mapActionDesc($log->action);
                $modelName = ActionMapperHelper::mapModels($log->model_name);

                if ($log->model_name === 'customers') {
                    $customerCategory = $changes['customerCategory']['new'] ?? null;
                    if ($customerCategory === 'leads') {
                        $modelName = 'leads';
                    } elseif ($customerCategory === 'contact') {
                        $modelName = 'kontak';
                    }
                }

                $nameChange = $changes['name']['new'] 
                    ?? trim(($changes['first_name']['new'] ?? '') . ' ' . ($changes['last_name']['new'] ?? '')) 
                    ?: null;

                if ($isSelfUpdate && ($log->model_name === 'users')){   
                    switch ($log->action) {
                        case 'CREATE':
                            $description = 'Register akun ' . ucfirst($log->user->first_name) . ' ' . ucfirst($log->user->last_name);
                            break;
                            
                        case 'UPDATE':
                            if (isset($changes['password'])){
                                $description = ucfirst($log->user->first_name) . ' ' . ucfirst($log->user->last_name) . " {$actionDesc} kata sandi";
                            } else {
                                $description = ucfirst($log->user->first_name) . ' ' . ucfirst($log->user->last_name) . " {$actionDesc} data diri";
                            }
                            break;
                        
                        case 'DELETE':
                            $description = ucfirst($modelName) . ' ' . ucfirst($log->user->first_name) . ' ' . ucfirst($log->user->last_name) . " {$actionDesc} akun"; 
                            break;

                        default:
                            return null;
                    }
                
                } else { 
                    $description = ucfirst($log->user->first_name) . ' ' . ucfirst($log->user->last_name) . " {$actionDesc} data {$modelName}";
                    if ($nameChange) {
                        $description .= " {$nameChange}";
                    }
                }

                return [
                    'title' => "{$actionTitle} Data - " . ucfirst($modelName),
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
                $description = ActionMapperHelper::mapDescription($log, $changes, $modelName);

            return [
                'title' => ActionMapperHelper::mapActionTitle($log->action) . " Data - " . ucfirst($modelName),
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