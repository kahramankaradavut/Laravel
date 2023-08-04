<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\ProjectDefination;
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


    $completionStatusCompleted = $selectedPerson->where('complation_status_color', 1)->count();
    $completionStatusDelayed = $selectedPerson->where('complation_status_color', 2)->count();
    $successRateGeneral = ($usageCount > 0) ? ($completionStatusCompleted / $usageCount) * 100 : 0;

    $abc = Task::where('assignees', 'like', '%' . $personName . '%')
        ->with('projectDefination') 
        ->get();
    
    $projectStatistics = $abc->groupBy('project_defination_id')->map(function ($abc) {
    $totalTasks = count($abc);
    $completedTasks = $abc->where('complation_status_color', 1)->count();
    $delayedTasks = $abc->where('complation_status_color', 2)->count();
    $successRate = ($totalTasks > 0) ? ($completedTasks / $totalTasks) * 100 : 0;

    return [
        'total_tasks' => $totalTasks,
        'completed_tasks' => $completedTasks,
        'delayed_tasks' => $delayedTasks,
        'success_rate' => $successRate,
    ];
});

    return view('products.person_details', compact('personName', 'usageCount', 'employeesName', 'projectStatistics', 'abc', 'completionStatusCompleted', 'completionStatusDelayed', 'successRateGeneral'));
}
    
    public function showTable(Request $request, $textInput=null)
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
                        'completed' => 0,
                        'delayed' => 0,
                    ];
                }
                if ($product->complation_status_color == 1) {
                    $completionStatus[$name]['completed']++;
                } if($product->complation_status_color == 2) {
                    $completionStatus[$name]['delayed']++;
                }
               
            }
        }
        $inputs = ProjectDefination::orderBy('uid', 'desc')->get();


        return view('products.bridge', compact('products', 'usageCount', 'completionStatus', 'textName', 'uploadDate', 'inputs', 'textInput'));
    }

  
}


