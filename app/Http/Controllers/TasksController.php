<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\ProjectDefination;
use App\Models\TaskEmployees;
use App\Models\Employees;
use Carbon\Carbon;

class TasksController extends Controller
{
    public function showPersonDetails($personName)
    {
        $employeesName = Employees::all();

        $selectedPerson = Task::where('assignees', 'like', '%' . $personName . '%')->get();
        if (!$selectedPerson) {
            abort(404);
        }
        $persons = explode(', ', $personName);

        foreach ($persons as $person) {
            $usageCount = Task::where('assignees', 'like', '%' . $person . '%')->count();
        }

        $completionStatusUndelayed = $selectedPerson->where('complation_status_color', 1)->count();
        $completionStatusDelayed = $selectedPerson->where('complation_status_color', 2)->count();
        $completionStatusCompleted = $selectedPerson->where('status', 'Done')->count();
        $successRateGeneral = $usageCount > 0 ? ($completionStatusUndelayed / $usageCount) * 100 : 0;

        $abc = Task::where('assignees', 'like', '%' . $personName . '%')
            ->with('projectDefination')
            ->get();

        $projectStatistics = $abc->groupBy('project_defination_id')->map(function ($abc) {
            $totalTasks = count($abc);
            $undelayedTasks = $abc->where('complation_status_color', 1)->count();
            $delayedTasks = $abc->where('complation_status_color', 2)->count();
            $completedTasks = $abc->where('status', 'Done')->count();
            $successRate = $totalTasks > 0 ? ($undelayedTasks / $totalTasks) * 100 : 0;

            return [
                'total_tasks' => $totalTasks,
                'undelayed_tasks' => $undelayedTasks,
                'delayed_tasks' => $delayedTasks,
                'completed_tasks' => $completedTasks,
                'success_rate' => $successRate,
            ];
        });

        return view('products.person_details', compact('personName', 'usageCount', 'employeesName', 'projectStatistics', 'abc', 'completionStatusCompleted', 'completionStatusDelayed', 'completionStatusUndelayed', 'successRateGeneral'));
    }

    public function showTable(Request $request, $textInput = null)
    {
        $textInput = $request->textInput;
        $textName = ProjectDefination::where('uid', $textInput)->first();
        $uploadDate = Carbon::parse($textName->updated_at)->format('d.m.Y');
        $products = Task::where('project_defination_id', $textName->id)->get();

        $usageCount = [];
        foreach ($products as $product) {
            $names = explode(', ', $product->assignees);
            foreach ($names as $name) {
                if (!isset($usageCount[$name])) {
                    $usageCount[$name] = 0;
                }
                $usageCount[$name]++;
            }
        }

        $completionStatus = [];
        foreach ($products as $product) {
            $names = explode(', ', $product->assignees);
            foreach ($names as $name) {
                if (!isset($completionStatus[$name])) {
                    $completionStatus[$name] = [
                        'undelayed' => 0,
                        'delayed' => 0,
                        'completed' => 0,
                    ];
                }
                if ($product->complation_status_color == 1) {
                    $completionStatus[$name]['undelayed']++;
                }
                if ($product->complation_status_color == 2) {
                    $completionStatus[$name]['delayed']++;
                }
                if ($product->status === 'Done') {
                    $completionStatus[$name]['completed']++;
                }
            }
        }

        $inputs = ProjectDefination::orderBy('uid', 'desc')->get();

        return view('products.bridge', compact('products', 'usageCount', 'completionStatus', 'textName', 'uploadDate', 'inputs', 'textInput'));
    }

    public function allProjects()
    {
        $allProjectsId = ProjectDefination::orderBy('id')->pluck('id', 'name');

        $projectDetails = []; 
        foreach ($allProjectsId as $projectName => $projectId) {
            $details = Task::where('project_defination_id', $projectId)
            ->with('projectDefination')
            ->get();

            $projectDetails[] = $details->groupBy('project_defination_id')->map(function ($details) {
                $totalTasks = count($details);
                $undelayedTasks = $details->where('complation_status_color', 1)->count();
                $delayedTasks = $details->where('complation_status_color', 2)->count();
                $completedTasks = $details->where('status', 'Done')->count();
                $successRate = $totalTasks > 0 ? ($undelayedTasks / $totalTasks) * 100 : 0;
                $name = $details->first()->projectDefination->name;
                $uid = $details->first()->projectDefination->uid;
                

                $idValues = $details->pluck('id')->toArray();
                    $employeeIds = TaskEmployees::whereIn('task_id', $idValues)
                        ->pluck('employee_id')
                        ->toArray();
                $uniqueEmployeeIds = collect($employeeIds)->unique()->values()->all();

                return [
                    'project_name' => $name,
                    'uid' => $uid,
                    'employees_count' => count($uniqueEmployeeIds),
                    'total_tasks' => $totalTasks,
                    'undelayed_tasks' => $undelayedTasks,
                    'delayed_tasks' => $delayedTasks,
                    'completed_tasks' => $completedTasks,
                    'success_rate' => $successRate,
                ];
            });
        }
    


        return view('products.allProjectsDetails', compact('projectDetails', 'details'));
    }

    public function allEmployees() {
        $employees = Employees::orderBy('id')->pluck('name', 'id');

        $employeesDetails = [];
        foreach ($employees as $employeeId => $employeeName) {


            if (!isset($employeesDetails[$employeeName])) {

                $name = $employeeName;
                $completedTasks = Task::where('assignees', 'like', '%' . $employeeName . '%')->where('status', 'Done')->count();
                $comlationStatusDelayed = Task::where('assignees', 'like', '%' . $employeeName . '%')->where('complation_status_color', 2)->count();
                $completionStatusUndelayed = Task::where('assignees', 'like', '%' . $employeeName . '%')->where('complation_status_color', 1)->count();
                $jobCount = TaskEmployees::where('employee_id', $employeeId)->count();
                $successRate = $jobCount > 0 ? ($completionStatusUndelayed / $jobCount) * 100 : 0;
                $employeesDetails[$employeeName] = [
                    'name' => $employeeName,
                    'job_count' => $jobCount,
                    'completed_task' => $completedTasks,
                    'undelayed_task' =>  $completionStatusUndelayed,
                    'delayed_task' =>  $comlationStatusDelayed,
                    'success_rate' =>  $successRate,
                ];
            }
           
        }

        $inputs = ProjectDefination::orderBy('uid', 'desc')->get();

        return view('products.allEmployees', compact('employeesDetails','successRate','jobCount','completionStatusUndelayed','comlationStatusDelayed','completedTasks','name','inputs'));
    }
}
