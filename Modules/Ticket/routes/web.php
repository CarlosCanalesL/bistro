<?php

use Illuminate\Support\Facades\Route;
use Modules\Ticket\Http\Controllers\TicketController;
use Modules\Ticket\Http\Controllers\FairController;
use Modules\Ticket\Http\Controllers\StationController;
use Modules\Ticket\Http\Controllers\ProductController;

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::resource('tickets', TicketController::class)->names('ticket');
// });

Route::middleware('auth')->prefix('ticket')->group(function () {
    Route::resource('/fair', FairController::class)->except(['show']);
    Route::resource('/station', StationController::class)->except(['show']);
    Route::resource('/product', ProductController::class)->except(['show']);
    Route::resource('/ticket', TicketController::class)->except(['show']);

    Route::controller(ProductController::class)->group(function () {
        Route::get('/product/list/{status}', 'list')->name('product.list');
    });
});
