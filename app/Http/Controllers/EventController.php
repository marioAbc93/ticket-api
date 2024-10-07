<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
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
