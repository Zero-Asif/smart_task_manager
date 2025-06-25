<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\GoogleCalendarController;
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

// --- সবার জন্য খোলা রুট (গেস্ট এবং লগইন করা ব্যবহারকারী) ---
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');

// ইমেইল থেকে টাস্ক সম্পন্ন করার জন্য বিশেষ রুট (লগইন করা আবশ্যক নয়)
Route::get('/tasks/{task}/complete-from-email', [TaskController::class, 'markAsCompleteFromEmail'])
    ->middleware('signed')
    ->name('tasks.complete.email');

Route::get('/tasks/{task}/start', [TaskController::class, 'startTask'])
    ->middleware('signed')
    ->name('tasks.start');

// --- শুধুমাত্র লগইন করা ব্যবহারকারীদের জন্য রুট ---
Route::middleware(['auth'])->group(function () {
    
    // Profile সম্পর্কিত সব রুট
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::patch('/profile/settings', [ProfileController::class, 'updateNotificationSettings'])->name('profile.settings.update');
    // Route::post('/phone/send-otp', [ProfileController::class, 'sendPhoneVerificationOtp'])->name('phone.send.otp');
    // Route::post('/phone/verify-otp', [ProfileController::class, 'verifyPhoneOtp'])->name('phone.verify.otp');
    Route::get('/api/user/verification-status', [ProfileController::class, 'verificationStatus'])->name('api.user.verification-status');

    // টাস্ক সম্পর্কিত অন্যান্য রুট
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::patch('/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::patch('tasks/{task}/toggle', [TaskController::class, 'toggleComplete'])->name('tasks.toggleComplete');
    Route::post('tasks/quick-store', [TaskController::class, 'quickStore'])->name('tasks.quickStore');
    
    // ক্যালেন্ডার লিংক থেকে টাস্ক 'In Progress' করার রুট
    // Route::get('/tasks/{task}/start', [TaskController::class, 'startTask'])->name('tasks.start');
    
    // User Import/Export রুট
    Route::get('/export-users', [UserController::class, 'exportUsers'])->name('users.export');
    Route::post('/import-users', [UserController::class, 'importUsers'])->name('users.import');

    // Activity Undo রুট
    Route::post('activities/{activity}/undo', [ActivityController::class, 'undo'])->name('activities.undo');

    // Google Calendar Integration রুট
    Route::get('/google-redirect', [GoogleCalendarController::class, 'redirect'])->name('google.redirect');
    Route::get('/google-callback', [GoogleCalendarController::class, 'callback'])->name('google.callback');
});

// Laravel-এর ডিফল্ট auth রুটগুলো লোড করার জন্য
require __DIR__.'/auth.php';
