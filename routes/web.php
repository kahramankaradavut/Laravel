<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\TasksController;



Route::get('/import-excel-page', function () {
    return view('products.index');
});


Route::get('/import-page', [ExcelController::class, 'importpage'])->name('importpage');
Route::post('/import-excel/{text_input}', [ExcelController::class, 'importExcel'])->name('import.excel');
Route::get('/view-project/{textInput?}', [TasksController::class, 'showTable'])->name('show.table');
Route::get('/person-details/{personName?}', [TasksController::class, 'showPersonDetails'])->name('person.details');


