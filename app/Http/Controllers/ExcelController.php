<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Employees;
use App\Models\ProjectDefination;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UserImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExcelController extends Controller
{
    public function importpage()
    {
        $inputs = ProjectDefination::orderBy('uid', 'desc')->get();
        $uploadDate = ProjectDefination::all();

        return view('products.import', compact('inputs'));
    }

    public function importExcel(Request $request)
    {

        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,txt,csv',
            'text_input' => 'required|string',
        ]);

        $textInput = $request->input('text_input');
        if (!$textInput) {
            abort(404);
        }
        $existingData = ProjectDefination::where('name', $textInput)->exists();
        if ($existingData) {
            return redirect()->back()->withErrors(['message' => 'This name is already registered. Try a different name.']);
        }
        $uid = Str::uuid();
        $mainTable = ProjectDefination::create([
            'name' => $textInput,
            'uid' => $uid,
        ]);
        $uidURL = $uid;

        $file = $request->file('excel_file');

        Excel::import(new UserImport($mainTable->id), $file);

        $products = Task::where('project_defination_id', $mainTable->id)->get();
        
        
       


            foreach ($products as $product) {
                $names = explode(', ', $product->assignees);
                foreach ($names as $name) {
                $employee = Employees::firstOrCreate(['name' => $name]);
                }
            }

            $existingTaskIds = DB::table('task_employees')
            ->pluck('task_id')
            ->toArray();


            $tasks = Task::all();

foreach ($tasks as $task) {
    $employeeNames = explode(',', $task->assignees); 

    foreach ($employeeNames as $employeeName) {
        $employeeName = trim($employeeName); 

        $employee = Employees::where('name', $employeeName)->first();

        if ($employee) {
            $existingTaskIds = DB::table('task_employees')
                ->where('employee_id', $employee->id)
                ->pluck('task_id')
                ->toArray();

            if (!in_array($task->id, $existingTaskIds)) {
                DB::table('task_employees')->insert([
                    'employee_id' => $employee->id,
                    'task_id' => $task->id
                ]);
            }
        } else {
            $defaultEmployeeId = 0;
            $existingTaskIds = DB::table('task_employees')
                ->where('employee_id', $defaultEmployeeId)
                ->pluck('task_id')
                ->toArray();

            if (!in_array($task->id, $existingTaskIds)) {
                DB::table('task_employees')->insert([
                    'employee_id' => $defaultEmployeeId,
                    'task_id' => $task->id
                ]);
            }
        }
    }
}

            

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
                } if($product->complation_status_color == 2) {
                    $completionStatus[$name]['delayed']++;
                }
                if($product->status === 'Done') {
                    $completionStatus[$name]['completed']++;
                }
               
            }
        }
  
        $inputs = ProjectDefination::orderBy('uid', 'desc')->get();
        $passwordCheck = env('DELETE_PASSWORD');


        return view('products.index', compact('products', 'usageCount', 'passwordCheck', 'completionStatus', 'textInput', 'inputs', 'uidURL'));
    }
}