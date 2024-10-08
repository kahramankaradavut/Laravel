<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\TasksController;

Route::get('/import-excel-page', function () {
    return view('products.index');
});

Route::get('/import-page', [ExcelController::class, 'importpage'])->name('importpage');
Route::match(['post'], '/import-excel/{text_input}', [ExcelController::class, 'importExcel'])
    ->name('import.excel');
    Route::any('/import-excel/{text_input}', function () {
        abort(404);
    });
Route::get('/view-project/{textInput?}', [TasksController::class, 'showTable'])->name('show.table');
Route::post('/delete-data-employee', [DataController::class, 'deleteDataEmployee'])->name('dataDeleteEmployee');
Route::post('/delete-data-project', [DataController::class, 'deleteDataProject'])->name('deleteDataProject');
Route::get('/all-projects', [TasksController::class, 'allProjects'])->name('all.projects');
Route::get('/all-employees', [TasksController::class, 'allEmployees'])->name('all.employees');
Route::get('/person-details/{personName?}', [TasksController::class, 'showPersonDetails'])->name('person.details');


