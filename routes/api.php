<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\EventController;


Route::get('tickets',                [TicketController::class, 'index']);
Route::get('ticket/{id}',            [TicketController::class, 'show']);
Route::post('events/{eventId}/buy', [TicketController::class, 'buyTicket']);

Route::get('events',                 [EventController::class, 'index']);
Route::post('events',                [EventController::class, 'store']);
Route::put('events/{id}',            [EventController::class, 'update']);
Route::delete('events/{id}',         [EventController::class, 'destroy']);
Route::get('event/{id}',             [EventController::class, 'show']);
Route::get('events-name',            [EventController::class, 'getEventNames']);
Route::get('events/summary',         [EventController::class, 'getEventSummary']);
Route::get('events/get-all',         [EventController::class, 'getAll']);
