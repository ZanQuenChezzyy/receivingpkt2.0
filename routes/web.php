<?php

use App\Livewire\Frontend\Home;
use App\Livewire\Frontend\PublicMaterialIssueForm;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');

Route::get('/pengambilan-barang/mir', PublicMaterialIssueForm::class)->name('frontend.mir.create');

Route::get('/admin/material-issues/print-bulk', [\App\Http\Controllers\Admin\MaterialIssuePrintController::class, 'printBulk'])
    ->middleware(['web', 'auth'])
    ->name('filament.admin.resources.material-issues.print_bulk');

Route::get('/admin/material-issues/{materialIssue}/print', [\App\Http\Controllers\Admin\MaterialIssuePrintController::class, 'print'])
    ->middleware(['web', 'auth'])
    ->name('filament.admin.resources.material-issues.print');
