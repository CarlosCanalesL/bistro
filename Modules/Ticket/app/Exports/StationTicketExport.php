<?php

namespace Modules\Ticket\Exports;

use App\Models\StationTicket;
use Maatwebsite\Excel\Concerns\FromCollection;

class StationTicketExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return StationTicket::all();
    }
}
