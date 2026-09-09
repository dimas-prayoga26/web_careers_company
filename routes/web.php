<?php

use App\Http\Controllers\CareerController;
use App\Support\CareerBrand;
use Illuminate\Support\Facades\Route;

Route::get('/', [CareerController::class, 'index'])->name('careers.index');
Route::post('/apply', [CareerController::class, 'store'])->name('careers.store');
Route::get('/success', [CareerController::class, 'success'])->name('careers.success');
Route::get('/failed', [CareerController::class, 'failed'])->name('careers.failed');

if (app()->environment('local')) {
    Route::get('/preview/email/applicant-status/{status?}', function (string $status = 'submitted') {
        $statusValue = match (strtolower($status)) {
            'interview' => 1,
            'diterima', 'accepted' => 2,
            default => 0,
        };

        return view('emails.applicants.status', [
            'brand' => CareerBrand::resolve(request()),
            'statusValue' => $statusValue,
            'applicantName' => 'Dwi Saputra',
            'positionName' => 'IT Programmer',
            'submittedAt' => now()->subDay(),
        ]);
    })->name('preview.email.applicant-status');
}
