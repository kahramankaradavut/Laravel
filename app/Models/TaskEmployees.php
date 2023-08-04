<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskEmployees extends Model
{
    protected $fillable = [
        'employee_id',
        'task_id',
    ]; 
}
