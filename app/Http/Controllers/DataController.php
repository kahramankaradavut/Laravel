<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Employees;
use Illuminate\Http\Request;
use App\Models\TaskEmployees;
use App\Models\ProjectDefination;
use Illuminate\Support\Facades\Session;

class DataController extends Controller
{
    public function deleteData(Request $request)
    {
        $password = $request->input('password');
        $dataType = $request->input('type');
        $dataId = $request->input('id');
        $status = 0;


        if ($password === env('DELETE_PASSWORD')) {
            if ($dataType === 'user') {
                // Kullanıcı verilerini silme işlemi
                Employees::where('id', $dataId)->delete();
                Session::flash('success', 'Kullanıcı verileri başarıyla silindi.');
                $status = 1;
            } elseif ($dataType === 'project') {
                // Proje verilerini silme işlemi
                ProjectDefination::where('id', $dataId)->delete();
                Session::flash('success', 'Proje verileri başarıyla silindi.');
                $status = 1;
            }
        } else {
            Session::flash('error', 'Geçersiz şifre. Veriler silinemedi.');
        }

        return response()->json(['status' => $status]);
    }

    public function deleteDataProject(Request $request)
    {
        $password = $request->input('password');
        $status = 0;

        if ($password == env('DELETE_PASSWORD')) {
            $dataName = $request->input('project_name');
            $project = ProjectDefination::where('name', $dataName)->first();

            $taskEmployee = Task::where('project_defination_id', $project->id)->get();

            foreach ($taskEmployee as $task) {
                TaskEmployees::where('task_id', $task->id)->delete();
            }

            ProjectDefination::where('name', $dataName)->delete();
            Task::where('project_defination_id', $project->id)->delete();
            $status = 1;
        } else {
            Session::flash('error', 'Geçersiz şifre. Veriler silinemedi.');
        }


        return response($status);
    }

    public function deleteDataEmployee(Request $request)
    {
        $password = $request->input('password');
        $status = 0;

        if($password === env('DELETE_PASSWORD')) {
            $dataId = $request->input('person_id');
            $dataName = $request->input('person_name');
    
            Employees::where('id', $dataId)->delete();
            TaskEmployees::where('employee_id', $dataId)->delete();
            $tasks = Task::all();
    
            foreach ($tasks as $task) {
                $assignees = explode(', ', $task->assignees);
    
                $index = array_search($dataName, $assignees);
                if ($index !== false) {
                    unset($assignees[$index]);
                }
    
                $task->assignees = implode(', ', $assignees);
                $task->save();
    
                if (empty($task->assignees)) {
                    $task->delete();
                }
            }
            $status = 1;
        } else {
            Session::flash('error', 'Geçersiz şifre. Veriler silinemedi.');
        }
        
        return response($status);
    }

}
