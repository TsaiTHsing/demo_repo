<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
    
});

Route::name('project_')->group(function () {
    Route::resource('info', ProjectController::class)->except(['show']);
    Route::get('project/showPdf/{id}', [ProjectController::class, 'showPdf']);
    Route::get('project/downloadFile/{id}', [ProjectController::class, 'downloadFile']);

    Route::resource('inter_meeting', MeetingController::class); // outer_meeting也用這隻, 帶變數區分
    Route::get('meeting/showPdf/{id}', [MeetingController::class, 'showPdf']);
    Route::get('meeting/downloadFile/{id}', [MeetingController::class, 'downloadFile']);

    Route::resource('task', TaskController::class)->except(['show']); // 程式開發
    Route::get('sub_func_file/downloadFile/{id}', [TaskController::class, 'downloadFile']);
    
});


