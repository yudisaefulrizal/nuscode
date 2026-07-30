<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageProcessingController; 
Route::get('/', function () {
    return view('welcome');
});
Route::get('/artikel/osi-layers', function () {
    return view('osilayer');
});
Route::get('/alatpesta', function () {
    return view('welcome');
});
Route::get('/artikel/Cyber-Security-Digital-Twin', function () {
    return view('Cyber-Security-Digital-Twin');
});
Route::get('/chatbot', function () {
    return view('chatbot');
});
Route::get('/review-jurnal', function () {
    return view('review-jurnal');
});
// Route untuk image processing
Route::post('/remove-background', [ImageProcessingController::class, 'removeBackground']);
Route::get('/api/image/test', [ImageProcessingController::class, 'testEndpoint']);

// CORS headers untuk semua response
Route::middleware(['cors'])->group(function () {
    // Routes di sini akan mendapatkan CORS headers
});
Route::get('/artikel/digital-twin-monitoring-device', function () {
    return view('monitoring');
});
Route::get('/artikel/rancang-bangun-agent-router-sub-agent', function () {
    return view('rancang-bangun-agent-router-sub-agent');
});
