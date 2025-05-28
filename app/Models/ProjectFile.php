<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectFile extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'project_id',
        'path',
        'fname',
        'format',

    ];


    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
