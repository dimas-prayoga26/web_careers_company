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
        $statuses = [
            0 => 'Submitted',
            1 => 'HR Interview',
            2 => 'Technical test',
            3 => 'User Interview',
            4 => 'Offering',
            5 => 'Not Suitable',
        ];

        $normalizedStatus = str_replace('_', '-', strtolower($status));
        $statusValue = is_numeric($status) ? (int) $status : match ($normalizedStatus) {
            'hr', 'hr-interview', 'interview' => 1,
            'technical', 'technical-test' => 2,
            'user', 'user-interview' => 3,
            'offering' => 4,
            'not-suitable', 'rejected' => 5,
            default => 0,
        };
        $statusValue = array_key_exists($statusValue, $statuses) ? $statusValue : 0;

        return view('emails.applicants.status', [
            'brand' => CareerBrand::resolve(request()),
            'statusValue' => $statusValue,
            'statusName' => $statuses[$statusValue],
            'applicantName' => 'Dwi Saputra',
            'positionName' => 'IT Programmer',
            'submittedAt' => now()->subDay(),
        ]);
    })->name('preview.email.applicant-status');
}
