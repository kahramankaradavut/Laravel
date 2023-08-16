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
        $employeeId = Employees::where('name', $personName)->first();

        if ($employeeId) {
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
            $finished = $completionStatusCompleted - ( $completionStatusUndelayed + $completionStatusDelayed);
            $successRateGeneral = $completionStatusCompleted > 0 ? (($finished*(env("DIFFERENCE_MULTIPLIER")) + $completionStatusDelayed*(env("DELAY_MULTIPLIER")) + $completionStatusUndelayed*(env("UNDELAY_MULTIPLIER"))) / $completionStatusCompleted) * 100 : 0;

            $abc = Task::where('assignees', 'like', '%' . $personName . '%')
                ->with('projectDefination')
                ->get();

            $projectStatistics = $abc->groupBy('project_defination_id')->map(function ($abc) {
                $totalTasks = count($abc);
                $undelayedTasks = $abc->where('complation_status_color', 1)->count();
                $delayedTasks = $abc->where('complation_status_color', 2)->count();
                $completedTasks = $abc->where('status', 'Done')->count();
                $finished = $completedTasks - ($undelayedTasks + $delayedTasks);
                $successRate = $completedTasks > 0 ? (($finished*(env("DIFFERENCE_MULTIPLIER")) + $delayedTasks*(env("DELAY_MULTIPLIER")) + $undelayedTasks*(env("UNDELAY_MULTIPLIER"))) / $completedTasks) * 100 : 0;

                return [
                    'total_tasks' => $totalTasks,
                    'undelayed_tasks' => $undelayedTasks,
                    'delayed_tasks' => $delayedTasks,
                    'completed_tasks' => $completedTasks,
                    'success_rate' => $successRate,
                ];
            });
            $passwordCheck = env('DELETE_PASSWORD');

            return view('products.person_details', compact('personName', 'passwordCheck', 'usageCount', 'employeesName', 'projectStatistics', 'abc', 'completionStatusCompleted', 'completionStatusDelayed', 'completionStatusUndelayed', 'successRateGeneral', 'employeeId'));
        } else {
            $errorMessage = 'This Employee Has Been Deleted!';
            return view('products.employeeError', compact('errorMessage'));
        }
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
        $passwordCheck = env('DELETE_PASSWORD');

        return view('products.bridge', compact('products', 'passwordCheck', 'usageCount', 'completionStatus', 'textName', 'uploadDate', 'inputs', 'textInput'));
    }

    public function allProjects()
    {
        $anyProject = ProjectDefination::all()->first();
        if ($anyProject !== null) {
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
                    $finished = $completedTasks - ($undelayedTasks + $delayedTasks);
                    $successRate = $completedTasks > 0 ? (($finished*(env("DIFFERENCE_MULTIPLIER")) + $delayedTasks*(env("DELAY_MULTIPLIER")) + $undelayedTasks*(env("UNDELAY_MULTIPLIER"))) / $completedTasks) * 100 : 0;
                    $name = $details->first()->projectDefination->name;
                    $uid = $details->first()->projectDefination->uid;

                    $idValues = $details->pluck('id')->toArray();
                    $employeeIds = TaskEmployees::whereIn('task_id', $idValues)
                        ->pluck('employee_id')
                        ->toArray();
                    $uniqueEmployeeIds = collect($employeeIds)
                        ->unique()
                        ->values()
                        ->all();

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
        } else {
            $errorMessage = 'Project Data Not Found!';
            return view('products.projectError', compact('errorMessage'));
        }
    }

    public function allEmployees()
    {
        $anyEmployees = Employees::all()->first();

        if ($anyEmployees !== null) {
            $employees = Employees::orderBy('id')->pluck('name', 'id');
            $employeesDetails = [];
            foreach ($employees as $employeeId => $employeeName) {
                if (!isset($employeesDetails[$employeeName])) {
                    $name = $employeeName;
                    $completedTasks = Task::where('assignees', 'like', '%' . $employeeName . '%')
                        ->where('status', 'Done')
                        ->count();
                    $comlationStatusDelayed = Task::where('assignees', 'like', '%' . $employeeName . '%')
                        ->where('complation_status_color', 2)
                        ->count();
                    $completionStatusUndelayed = Task::where('assignees', 'like', '%' . $employeeName . '%')
                        ->where('complation_status_color', 1)
                        ->count();
                    $jobCount = TaskEmployees::where('employee_id', $employeeId)->count();
                    $finished = $completedTasks - ($comlationStatusDelayed  + $completionStatusUndelayed);
                    $successRate = $completedTasks > 0 ? (($finished*(env("DIFFERENCE_MULTIPLIER")) + $comlationStatusDelayed*(env("DELAY_MULTIPLIER")) + $completionStatusUndelayed*(env("UNDELAY_MULTIPLIER"))) / $completedTasks) * 100 : 0;
                    $employeesDetails[$employeeName] = [
                        'name' => $employeeName,
                        'job_count' => $jobCount,
                        'completed_task' => $completedTasks,
                        'undelayed_task' => $completionStatusUndelayed,
                        'delayed_task' => $comlationStatusDelayed,
                        'success_rate' => $successRate,
                    ];
                }
            }

            $inputs = ProjectDefination::orderBy('uid', 'desc')->get();

            return view('products.allEmployees', compact('employeesDetails', 'successRate', 'jobCount', 'completionStatusUndelayed', 'comlationStatusDelayed', 'completedTasks', 'name', 'inputs'));
        } else {
            $errorMessage = 'Employee Data Not Found!';
            return view('products.employeeError', compact('errorMessage'));
        }
    }

    public function deleteDataEmployee(Request $request)
    {
        $password = $request->input('password');
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
        return response()->json(['success' => true, 'message' => 'Kullanıcı verileri başarıyla silindi.']);
    }

    public function deleteDataProject(Request $request)
    {
        $password = $request->input('password');
        $dataName = $request->input('project_name');
        $project = ProjectDefination::where('name', $dataName)->first();

        $taskEmployee = Task::where('project_defination_id', $project->id)->get();

        foreach ($taskEmployee as $task) {
            TaskEmployees::where('task_id', $task->id)->delete();
        }

        ProjectDefination::where('name', $dataName)->delete();
        Task::where('project_defination_id', $project->id)->delete();

        return response()->json(['success' => true, 'message' => 'Kullanıcı verileri başarıyla silindi.']);
    }
}
