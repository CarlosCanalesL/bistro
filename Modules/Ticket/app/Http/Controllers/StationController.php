<?php

namespace Modules\Ticket\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Ticket\Models\Station;
use modules\Ticket\Http\Requests\StationRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use Modules\Ticket\Traits\SetFilterQuery;

class StationController extends Controller
{
    use SetFilterQuery;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DB::table('stations')->when($request->get('search'), function ($query, $search) {
            return $query->where(function ($query) use ($search) {
                foreach ($search as $field => $value) {
                    $filter = $this->setField($field);

                    if (!is_null($filter) && !is_null($value)) {
                        $this->setFilter($query, $filter['operator'], $filter['field'], $value);
                    }
                }
            });
        })->when($request->get('sort'), function ($query, $sortBy) {
            return $query->orderBy($sortBy['key'], $sortBy['order']);
        });

        $result = $query
            ->select('station_id', 'station_name', 'status')
            ->paginate($request->get('limit', 10));

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return Inertia::render('Ticket/Station/Index', [
            'result' => $result
        ]);
    }

    /**
     * List categories load resource
     */
    public function list($enabled)
    {
        $stations =  DB::table('stations')
            ->where(function ($query) use ($enabled) {
                if ($enabled === 'A') {
                    $query->where('status', true);
                }
            })
            ->select('station_id', 'station_name')
            ->orderBy('station_name', 'asc')
            ->get();

        return response()->json(['stations' => $stations]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Ticket/Station/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StationRequest $request)
    {
        $station = $this->setDataStore($request);
        $stationId = DB::table('stations')->insertGetId($station);

        $message = sprintf('La estacion %s, ha sido ingresada exitosamente.', $station['station_name']);

        if ($request->expectsJson()) {
            if ($stationId) {
                $newStation = (object) $station;
                $newStation->station_id = $stationId;
            }

            return response()->json([
                'message' => $message,
                'station' => $newStation
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Station $station): Response
    {
        return Inertia::render('Ticket/Station/Edit', [
            'station' => $station
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StationRequest $request, Station $station)
    {
        $station->update($request->all());

        return redirect()->back()->with('success', sprintf('Actualizado con éxito, la estacion %s', $station->station_name));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Station $station)
    {
        $station->delete();

        return redirect()->back()->with('success', sprintf('Eliminado con éxito, la estacion %s', $station->station_name));
    }

    protected function setDataStore($request)
    {
        $user = Auth::user();
        $current_date = $this->getCurrentDate()->format('Y-m-d H.i:s');

        return [
            'station_name' => $request->get('station_name'),
            'status' => $request->get('status'),
            'user_id' => $user->user_id,
            'created_at' => $current_date,
            'updated_at' => $current_date
        ];
    }

    protected function getCurrentDate()
    {
        return Carbon::now()->setTimezone(config('app.timezone'));
    }

    /**
     * @param type $field
     * @return type
     */
    protected function setField($field): array
    {
        $fieldList = [
            'station_name' => [
                'field' => 'station_name',
                'operator' => 'like'
            ],
            'status' => [
                'field' => 'status',
                'operator' => 'equal'
            ]
        ];

        return $fieldList[$field] ?? null;
    }
}
