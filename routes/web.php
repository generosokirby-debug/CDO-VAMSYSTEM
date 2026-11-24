<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminPageController;
use App\Http\Controllers\CdoRecordController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\VehicleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return view('login');
})->middleware('guest')->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt([...$credentials, 'status' => 'Active'], $request->boolean('remember'))) {
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
})->middleware('guest');

Route::middleware(['auth', "can:module-access,'dashboard'"])->group(function (): void {
    Route::get('/', AdminDashboardController::class)->name('dashboard');
    Route::get('/dashboard', fn () => redirect()->route('dashboard'))->name('dashboard.legacy');
});

Route::middleware(['auth', "can:module-access,'employees'"])->group(function (): void {
    Route::resource('employees', EmployeeController::class)->except(['destroy']);
    Route::resource('cdos', CdoRecordController::class)
        ->except(['index', 'show'])
        ->parameters(['cdos' => 'cdoRecord']);
});

Route::middleware(['auth', "can:module-access,'vehicles'"])->group(function (): void {
    Route::get('/vehicles/export', [VehicleController::class, 'export'])->name('vehicles.export');
    Route::post('/vehicles/{vehicle}/status', [VehicleController::class, 'updateStatus'])->name('vehicles.status');
    Route::resource('vehicles', VehicleController::class)->except(['destroy']);
});

Route::middleware(['auth', "can:module-access,'logs'"])->group(function (): void {
    Route::get('/logs/export', [LogController::class, 'export'])->name('logs.export');
    Route::get('/logs/{gateScan}', [LogController::class, 'show'])->name('logs.show');
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
});

Route::middleware(['auth', 'can:admin-panel', "can:module-access,'settings'"])->group(function (): void {
    Route::get('/settings', [AdminPageController::class, 'settings'])->name('settings.index');
    Route::post('/settings/users', [AdminPageController::class, 'storeUser'])->name('settings.users.store');
    Route::patch('/settings/users/{user}', [AdminPageController::class, 'updateUser'])->name('settings.users.update');
    Route::post('/settings/users/{user}/overrides', [AdminPageController::class, 'updateUserOverrides'])->name('settings.users.overrides');
    Route::post('/settings/permissions', [AdminPageController::class, 'updateRolePermissions'])->name('settings.permissions.update');
});

Route::middleware(['auth', 'can:admin-panel'])->group(function (): void {
    Route::get('/profile', [AdminPageController::class, 'profile'])->name('profile.show');
    Route::patch('/profile', [AdminPageController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AdminPageController::class, 'updatePassword'])->name('profile.password');
});

Route::get('/gate-scanner', [AdminPageController::class, 'gateScanner'])
    ->middleware(['auth', "can:module-access,'gate-scanner'"])
    ->name('gate-scanner.index');
Route::get('/my-vehicles', [AdminPageController::class, 'myVehicles'])
    ->middleware(['auth', "can:module-access,'my-vehicles'"])
    ->name('my-vehicles.index');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');
