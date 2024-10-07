<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\EventController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('tickets', [TicketController::class, 'index']);
Route::get('ticket/{id}', [TicketController::class, 'show']);
Route::post('ticket', [TicketController::class, 'buyTicket']);

Route::get('events',  [EventController::class, 'index']);
Route::get('event/{show}',  [EventController::class, 'show']);
Route::get('events-name',  [EventController::class, 'getEventNames']);
