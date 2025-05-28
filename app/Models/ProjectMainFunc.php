<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectMainFunc extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'project_main_funcs';

    protected $fillable = [
        'project_id',
        'type',
        'template',
        'title',
        'is_menu',

    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function sub_funcs()
    {
        return $this->hasMany(ProjectSubFunc::class, 'main_func_id', 'id');
    }
}
