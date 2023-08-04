<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employees extends Model
{
    protected $fillable = [
        'name',
    ];



    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_employees', 'id', 'id');
    }

}
