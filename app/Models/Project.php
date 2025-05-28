<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'category',
        'project_status',
        'description',
        'color_code',
        'project_level',
    ];

    // public function project_status()
    // {
    //     return $this->belongsTo(ProjectStatus::class, 'project_status_id');
    // }

    public function schedule()
    {
        return $this->hasOne(ProjectSchedule::class, 'project_id');
    }

    public function project_users()
    {
        return $this->hasMany(ProjectUser::class, 'project_id');
    }

    public function files()
    {
        return $this->hasMany(ProjectFile::class, 'project_id');
    }

    public function inside_meetings()
    {
        return $this->hasMany(Meeting::class, 'project_id')->with(['editor', 'files'])->where('type', 1)->orderBy('meet_date', 'asc');
    }

    public function outside_meetings()
    {
        return $this->hasMany(Meeting::class, 'project_id')->with(['editor', 'files'])->where('type', 2)->orderBy('meet_date', 'asc');
    }

    public function main_funcs()
    {
        return $this->hasMany(ProjectMainFunc::class, 'project_id');
    }

    public function diaries()
    {
        return $this->hasMany(DailyReports::class, 'project_id');
    }

    public function sub_func_files()
    {
        return $this->hasMany(SubFuncFile::class, 'project_id');
    }

}
