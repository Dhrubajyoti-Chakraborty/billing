<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BillController;

Route::get('/', function () {
    return view('welcome');
});
Route::resource('bills', BillController::class);
