<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class ActivityLog extends Model
{
    use SoftDeletes, HasUuid;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'model_name',
        'action',
        'changes',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast to date instances.
     * 
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Get the user that owns the activitylogs.
     * 
     * This defines a many-to-one relationship where the user belongs to a company.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function getPaginatedLogs()
    {
        return self::with('user:id,first_name,last_name,email')
            ->orderBy('updated_at', 'desc')
            ->paginate(5);
    }

    public static function getLogsGroupedByMonth()
    {
        $paginatedLogs = self::getPaginatedLogs();
        $logs = $paginatedLogs->getCollection()->groupBy(function ($log) {
            return Carbon::parse($log->updated_at)->format('F Y');
        });

        return [
            'paginatedLogs' => $paginatedLogs,
            'logs' => $logs
        ];
    }
}
