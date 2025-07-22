<?php

namespace Modules\Ticket\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Ticket\Http\Requests\ReaderRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class ReaderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Ticket/Reader/Index');
    }

    public function validateTicket($uuid)
    {
        $success = true;
        $message = 'El producto escaneado se encuentra disponible para canjearlo';

        $ticket = $this->getTicket($uuid);

        switch ($ticket->status) {
            case 'C':
                $success = false;
                $message = 'El ticket escaneado, ya ha sido canjeado.';
                break;
            case 'A':
                $success = false;
                $message = 'El ticket escaneado, ha sido anulado.';
                break;
        }

        return response()->json([
            'success' => $success,
            'message' => $message,
            'ticket' => $ticket
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(ReaderRequest $request)
    {
        $station = $this->getStation();
        $dataStore = $this->setDataStore($request, $station->station_user_id);

        DB::transaction(function () use ($dataStore) {
            $stationTicketId = DB::table('station_tickets')->insertGetId($dataStore);

            if ($stationTicketId) {
                DB::table('tickets')->where('ticket_id', $dataStore['ticket_id'])->update([
                    'status' => 'C'
                ]);
            }
        });
    }

    private function setDataStore($request, $station_user_id)
    {
        $current_date = $this->getCurrentDate()->format('Y-m-d H.i:s');

        return [
            'ticket_id' => $request->get('ticket_id'),
            'station_user_id' => $station_user_id,
            'created_at' => $current_date,
            'updated_at' => $current_date,
        ];
    }

    private function getStation()
    {
        $user = Auth::user();

        return DB::table('station_users')
            ->where('user_id', $user->user_id)
            ->first();
    }

    private function getTicket($uuid)
    {
        return DB::table('v_tickets')
            ->where('uuid', 'like', '%' . $uuid . '%')
            ->select('ticket_id', 'product_name', 'uuid', 'unit_price', 'status')
            ->first();
    }

    protected function getCurrentDate()
    {
        return Carbon::now()->setTimezone(config('app.timezone'));
    }
}
