<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectSchedule extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'project_schedules';

    protected $fillable = [
        'project_id',
        'estimated_draft_delivery',
        'actual_draft_delivery',
        'final_version_date',
        'estimated_test_delivery',
        'actual_test_delivery',
        'client_acceptance_start',
        'client_acceptance_end',
        'official_launch_date',
        'warranty_start_date',
        'warranty_expiration_date',

    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

}
