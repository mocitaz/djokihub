<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\WorkRuleController;
use App\Http\Controllers\MeetingNoteController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SopSectionController;
use App\Http\Controllers\SopItemController;
use App\Http\Controllers\RegulationController;


Auth::routes(['register' => false]);

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::get('/home', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('projects', ProjectController::class);
    Route::get('/projects/{project}/poc/generate', [ProjectController::class, 'generatePocPdf'])->name('projects.poc.generate');
    Route::get('/projects/{project}/bast/generate', [ProjectController::class, 'generateBastPdf'])->name('projects.bast.generate');
    Route::get('/documents/select-project-for-poc', [ProjectController::class, 'listProjectsForPoc'])->name('documents.listPoc');
    Route::get('/documents/select-project-for-bast', [ProjectController::class, 'listProjectsForBast'])->name('documents.listBast');
    Route::resource('staff', StaffController::class)->parameters(['staff' => 'user']);
    Route::get('/work-rules', [WorkRuleController::class, 'index'])->name('work-rules');
    Route::resource('meeting-notes', MeetingNoteController::class);
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{project}/generate', [InvoiceController::class, 'generatePdf'])->name('invoices.generate');
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('sop-sections', SopSectionController::class);
        Route::resource('sop-items', SopItemController::class);
        Route::resource('regulations', RegulationController::class);
    });
});