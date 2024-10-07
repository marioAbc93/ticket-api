<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Sale;
use DB;

class TicketController extends Controller
{
    public function index()
    {
        try {
            $tickets = Ticket::paginate(10);
            return response()->json([
                'message' => 'Lista de tickets obtenida correctamente',
                'tickets' => $tickets->items(),
                'total_pages' => $tickets->lastPage(),
                'total_tickets' => $tickets->total(),
                'current_page' => $tickets->currentPage(),
            ], 200);
        } catch (Exception $exc) {
            return response()->json([
                'message' => 'Error al recuperar tickets',
                'error' => $exc->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            return response()->json(['message' => 'ticket obtenido correctamente', 'ticket' => $ticket], 200);
        } catch (Exception $exc) {
            return response()->json([
                'message' => 'Error al recuperar ticket',
                'error' => $exc->getMessage(),
            ], 500);
        }
    }

    public function buy(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);

        DB::transaction(function () use ($event, $eventId) {
            $soldTicketsCount = DB::table('tickets')
                                ->where('event_id', $eventId)
                                ->lockForUpdate()
                                ->count();

            if ($soldTicketsCount >= 10) {
                throw new \Exception('No hay más tickets disponibles para este evento.');
            }

            $ticket = Ticket::create([
                'event_id' => $event->id,
                'qr_code' => 'GENERATE_QR_CODE',
            ]);

            Sale::create([
                'ticket_id' => $ticket->id,
                'event_id' => $event->id,
            ]);
        });

        return response()->json(['message' => 'Compra de ticket realizada con éxito']);
    }

    public function destroy($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $ticket->delete();

            return response()->json(['message' => 'ticket eliminado correctamente'], 200);
        } catch (Exception $exc) {
            return response()->json([
                'message' => 'Error al eliminar ticket',
                'error' => $exc->getMessage(),
            ], 500);
        }
    }

}
