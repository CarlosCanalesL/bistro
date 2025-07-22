<?php

use Illuminate\Support\Facades\Route;
use Modules\Ticket\Http\Controllers\TicketController;
use Modules\Ticket\Http\Controllers\FairController;
use Modules\Ticket\Http\Controllers\StationController;
use Modules\Ticket\Http\Controllers\ProductController;
use Modules\Ticket\Http\Controllers\ReaderController;

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

    Route::controller(ReaderController::class)->group(function () {
        Route::get('/reader/index', 'index')->name('reader.index');
        Route::post('/reader/store', 'store')->name('reader.store');
        Route::get('/reader/ticket/{uuid}', 'validateTicket')->name('reader.validateTicket');
    });
});
