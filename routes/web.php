<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\AssessmentController;
use Illuminate\Support\Facades\Route;

// Main routes
Route::get('/', [AssessmentController::class, 'index'])->name('assessment.index');
Route::get('/test/{sessionId}', [AssessmentController::class, 'test'])->name('assessment.test');
Route::get('/result/{sessionId}', [AssessmentController::class, 'result'])->name('assessment.result');

// API routes
Route::prefix('api')->group(function () {
    Route::get('/languages', [ApiController::class, 'getLanguages']);
    Route::post('/session/start', [ApiController::class, 'startSession']);
    Route::get('/session/{sessionId}/questions', [ApiController::class, 'getSessionQuestions']);
    Route::post('/session/{sessionId}/answer', [ApiController::class, 'submitAnswer']);
    Route::post('/session/{sessionId}/timer', [ApiController::class, 'saveTimer']);
    Route::post('/session/{sessionId}/submit', [ApiController::class, 'submitTest']);
    Route::get('/session/{sessionId}', [ApiController::class, 'getSession']);
    Route::post('/session/{sessionId}/resume', [ApiController::class, 'uploadResume']);
});
