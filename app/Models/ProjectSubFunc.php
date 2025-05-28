<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectSubFunc extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'project_sub_funcs';
    protected $fillable = [
        'main_func_id',
        'user_id',
        'creator_id',
        'title',
        'is_menu',
        'task_level',
        'estimated_work_hour',
        'actual_work_hour',
        'estimated_date',
        'actual_date',
        'sort',
        'is_completed',

    ];

    public function main_func()
    {
        return $this->belongsTo(ProjectMainFunc::class, 'main_func_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function sub_func_description()
    {
        return $this->hasOne(SubFuncDescription::class, 'sub_func_id');
    }

    public function diaries()
    {
        return $this->hasMany(DailyReports::class, 'sub_func_id', 'id');
    }

    public function files()
    {
        return $this->hasMany(SubFuncFile::class, 'sub_func_id', 'id');
    }

}
