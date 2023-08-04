<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'assignees',
        'status',
        'labels',
        'start_date',
        'end_date',
        'due_date',
        'complation_status_color',
        'complation_status',
        'project_defination_id',
    ];

    public function employees()
    {
        return $this->belongsToMany(Employees::class, 'task_employees', 'id', 'id');

    }

    public function projectDefination()
    {
        return $this->belongsTo(ProjectDefination::class, 'project_defination_id');
    }

    public function getEmployeeNamesAttribute()
    {
        return explode(', ', $this->employees_name);
    }

}
