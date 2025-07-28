<?php

namespace Modules\Ticket\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class StationTicketExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return DB::table('v_station_tickets')
            ->when($this->request->get('station_id'), function ($query, $station_id) {
                return $query->where('station_id', $station_id);
            })
            ->when($this->request->get('product_id'), function ($query, $product_id) {
                return $query->where('product_id', $product_id);
            })
            ->when($this->request->get('start_created_at'), function ($query, $start_created_at) {
                return $query->where('created_at', '>=', $start_created_at);
            })
            ->when($this->request->get('end_created_at'), function ($query, $end_created_at) {
                return $query->where('created_at', '<=', $end_created_at);
            })
            ->select('station_name', 'product_name', 'unit_price', 'created_at')
            ->get();
    }

    /**
     * @return response()
     */
    public function headings(): array
    {
        return ['station_name', 'product_name', 'unit_price', 'created_at'];
    }
}
