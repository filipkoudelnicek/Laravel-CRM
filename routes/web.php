<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TimeEntryController;
use App\Http\Controllers\WorkTimeController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DashboardController;

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Clients
    Route::resource('clients', ClientController::class);

    // Projects
    Route::resource('projects', ProjectController::class);

    // Tasks
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::post('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');

    // Comments
    Route::post('/tasks/{task}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::post('/comments/{comment}/attachments', [CommentController::class, 'uploadAttachment'])->name('comments.attachments.store');

    // Time Tracking
    Route::get('/time-tracking', [TimeEntryController::class, 'index'])->name('time-tracking.index');
    Route::get('/reports', [TimeEntryController::class, 'reports'])->name('reports.index');

    // Work Time API
    Route::get('/api/work-time/active', [WorkTimeController::class, 'getActive'])->name('work-time.active');
    Route::post('/api/work-time/start', [WorkTimeController::class, 'start'])->name('work-time.start');
    Route::post('/api/work-time/{workTimeEntry}/pause', [WorkTimeController::class, 'pause'])->name('work-time.pause');
    Route::post('/api/work-time/{workTimeEntry}/resume', [WorkTimeController::class, 'resume'])->name('work-time.resume');
    Route::post('/api/work-time/{workTimeEntry}/stop', [WorkTimeController::class, 'stop'])->name('work-time.stop');

    // Notifications
    Route::get('/api/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::put('/api/notifications', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

    // Passwords
    Route::get('/passwords', [PasswordController::class, 'index'])->name('passwords.index');
    Route::get('/passwords/create', [PasswordController::class, 'create'])->name('passwords.create');
    Route::post('/passwords', [PasswordController::class, 'store'])->name('passwords.store');
    Route::get('/passwords/{password}/edit', [PasswordController::class, 'edit'])->name('passwords.edit');
    Route::put('/passwords/{password}', [PasswordController::class, 'update'])->name('passwords.update');
    Route::delete('/passwords/{password}', [PasswordController::class, 'destroy'])->name('passwords.destroy');
});

require __DIR__.'/auth.php';

