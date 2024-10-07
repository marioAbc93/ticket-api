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

            $eventData = $events->map(function ($event) {

                $totalTickets = Ticket::where('event_id', $event->id)->count();

                $soldTickets = Sale::where('event_id', $event->id)->count();

                $availableTickets = $totalTickets - $soldTickets;

                return [
                    'id' => $event->id,
                    'name' => $event->name,
                    'description' => $event->description,
                    'date' => $event->date,
                    'ticket_value' => $event->ticket_value,
                    'available_tickets' => $availableTickets,
                ];
            });

            return response()->json([
                'message' => 'Lista de eventos obtenida correctamente',
                'events' => $eventData,
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

    public function getEventNames()
    {
        try {
            $events = Event::whereHas('tickets', function ($query) {
                $query->doesntHave('sale');
            })
            ->select('name')
            ->paginate(10);

            return response()->json([
                'message' => 'Lista de eventos obtenida correctamente',
                'events' => $events,
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
