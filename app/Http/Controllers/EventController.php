<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Sale;

class EventController extends Controller
{
    public function index()
    {
        try {
            $events = Event::paginate(10);

            return response()->json([
                'message' => 'Lista de eventos obtenida correctamente',
                'events' => $events->items(),
                'total_pages' => $events->lastPage(),
                'total_events' => $events->total(),
                'current_page' => $events->currentPage(),
            ], 200);
        } catch (Exception $exc) {
            return response()->json([
                'message' => 'Error al recuperar eventos',
                'error' => $exc->getMessage(),
            ], 500);
        }
    }

    public function getAll()
    {
        try {
            $events = Event::all();

            return response()->json([
                'message' => 'Lista de eventos obtenida correctamente',
                'events' => $events,
            ], 200);
        } catch (Exception $exc) {
            return response()->json([
                'message' => 'Error al recuperar eventos',
            ])
        }
    }

    public function getEventSummary()
    {
        try {
            $allEvents = Event::count();

            $availableEvents = Event::where('available_tickets', ">", 0)->count();

            $soldoutEvents = Event::where('available_tickets', "=", 0)->count();

            $eventsWithAvailableTickets = Event::where('available_tickets', '>', 0)
                ->get(['id', 'name', 'available_tickets']);

            return response()->json([
                'message' => 'Lista de eventos obtenida correctamente',
                'total_events' => $allEvents,
                'available_events' => $availableEvents,
                'soldout_events' => $soldoutEvents,
                'events_with_available_tickets' => $eventsWithAvailableTickets,
            ], 200);
        } catch (Exception $exc) {
            return response()->json([
                'message' => 'Error al recuperar eventos',
                'error' => $exc->getMessage(),
            ], 500);
        }
    }


    public function getEventNames()
    {
        try {
            $events = Event::where('available_tickets', ">", 0)
            ->select('id', 'name', 'ticket_value')->get();

            return response()->json([
                'message' => 'Lista de eventos obtenida correctamente',
                'events' => $events,
            ], 200);
        } catch (Exception $exc) {
            return response()->json([
                'message' => 'Error al recuperar eventos',
                'error' => $exc->getMessage(),
            ], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $event = Event::create($request->all());

            return response()->json(['message' => 'Evento creado exitosamente', 'event' => $event], 201);
        } catch (Exception $exc) {
            return response()->json([
                'message' => 'Error al crear evento',
                'error' => $exc->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $event = Event::findOrFail($id);
            return response()->json(['message' => 'evento obtenido correctamente', 'event' => $event], 200);
        } catch (Exception $exc) {
            return response()->json([
                'message' => 'Error al recuperar el evento',
                'error' => $exc->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $event = Event::findOrFail($id);
            $event->delete();

            return response()->json(['message' => 'evento eliminado correctamente'], 200);
        } catch (Exception $exc) {
            return response()->json([
                'message' => 'Error al eliminar el evento',
                'error' => $exc->getMessage(),
            ], 500);
        }
    }
}
