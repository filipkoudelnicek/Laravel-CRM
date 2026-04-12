<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasswordEntryController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SupportPlanController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TimeEntryController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function () {

    Route::get('/', [HomeController::class, 'home']);
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('user-management',        [UserController::class, 'index'])->name('user-management');
    Route::get('users/create',             [UserController::class, 'create'])->name('users.create');
    Route::post('users',                   [UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit',        [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}',             [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}',          [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/logout', [SessionsController::class, 'destroy']);
    Route::get('/user-profile',  [InfoUserController::class, 'create']);
    Route::post('/user-profile', [InfoUserController::class, 'store']);
    Route::post('/toggle-dark-mode', function () {
        $user = auth()->user();
        $user->dark_mode = !$user->dark_mode;
        $user->save();
        return response()->json(['dark_mode' => $user->dark_mode]);
    })->name('toggle-dark-mode');

    // ── CRM ──────────────────────────────────────────────────

    // Clients
    Route::resource('clients', ClientController::class);
    Route::get('clients/{client}/modal', [ClientController::class, 'modalView'])->name('clients.modal');

    // Projects
    Route::resource('projects', ProjectController::class);
    Route::post('projects/{project}/users',           [ProjectController::class, 'attachUser'])->name('projects.attach-user');
    Route::delete('projects/{project}/users/{user}',  [ProjectController::class, 'detachUser'])->name('projects.detach-user');
    Route::get('projects/{project}/modal',            [ProjectController::class, 'modalView'])->name('projects.modal');

    // Tasks
    Route::get('tasks/{task}/modal',                       [TaskController::class, 'modalView'])->name('tasks.modal');
    Route::resource('tasks', TaskController::class);

    // Time Entries (scoped to task)
    Route::post('tasks/{task}/time-entries',        [TimeEntryController::class, 'store'])->name('tasks.time-entries.store');
    Route::put('tasks/{task}/time-entries/{timeEntry}', [TimeEntryController::class, 'update'])->name('tasks.time-entries.update');
    Route::delete('tasks/{task}/time-entries/{timeEntry}', [TimeEntryController::class, 'destroy'])->name('tasks.time-entries.destroy');

    // Comments (scoped to task)
    Route::post('tasks/{task}/comments',        [CommentController::class, 'store'])->name('tasks.comments.store');
    Route::put('comments/{comment}',            [CommentController::class, 'update'])->name('comments.update');
    Route::delete('comments/{comment}',         [CommentController::class, 'destroy'])->name('comments.destroy');

    // Password Vault
    Route::resource('passwords', PasswordEntryController::class);
    Route::post('passwords/{password}/reveal',  [PasswordEntryController::class, 'reveal'])->name('passwords.reveal');
    Route::get('passwords/{password}/modal',    [PasswordEntryController::class, 'modalContent'])->name('passwords.modal');

    // Invoices
    Route::resource('invoices', InvoiceController::class);

    // Support Plans
    Route::resource('support-plans', SupportPlanController::class);

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
});

Route::group(['middleware' => 'guest'], function () {
    // Registration disabled – new accounts are created by admin in CRM
    // Route::get('/register',                [RegisterController::class, 'create']);
    // Route::post('/register',               [RegisterController::class, 'store']);
    Route::get('/login',                   [SessionsController::class, 'create']);
    Route::post('/session',                [SessionsController::class, 'store']);
    Route::get('/login/forgot-password',   [ResetController::class, 'create']);
    Route::post('/forgot-password',        [ResetController::class, 'sendEmail']);
    Route::get('/reset-password/{token}',  [ResetController::class, 'resetPass'])->name('password.reset');
    Route::post('/reset-password',         [ChangePasswordController::class, 'changePassword'])->name('password.update');
});

Route::get('/login', fn () => view('session/login-session'))->name('login');
