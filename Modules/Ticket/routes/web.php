<?php

use Illuminate\Support\Facades\Route;
use Modules\Ticket\Http\Controllers\TicketController;
use Modules\Ticket\Http\Controllers\ProductController;

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::resource('tickets', TicketController::class)->names('ticket');
// });

Route::middleware('auth')->prefix('ticket')->group(function() {
    Route::resource('/product',ProductController::class)->except(['show']);

});
