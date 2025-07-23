<?php

namespace Modules\Ticket\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Ticket\Models\StationUser;
use Modules\Ticket\Http\Requests\StationUserRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

use Modules\Ticket\Traits\SetFilterQuery;

class StationUserController extends Controller
{
    use SetFilterQuery;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DB::table('v_station_users')->when($request->get('search'), function ($query, $search) {
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
            ->select('station_user_id', 'station_name', 'name')
            ->paginate($request->get('limit', 10));

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return Inertia::render('Ticket/StationUser/Index', [
            'result' => $result
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Ticket/StationUser/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StationUserRequest $request)
    {
        $stationUser = $this->setDataStore($request);
        $stationUserId = DB::table('station_users')->insertGetId($stationUser);

        $message = sprintf('La estacion y el usuario %s, ha sido ingresada exitosamente.', $stationUser['station_id']);

        if ($request->expectsJson()) {
            if ($stationUserId) {
                $newStationUser = (object) $stationUser;
                $newStationUser->stationUser_id = $stationUserId;
            }

            return response()->json([
                'message' => $message,
                'stationUser' => $newStationUser
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StationUser $stationUser): Response
    {
        return Inertia::render('Ticket/StationUser/Edit', [
            'stationUser' => $stationUser
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StationUserRequest $request, StationUser $stationUser)
    {
        $stationUser->update($request->all());

        return redirect()->back()->with('success', sprintf('Actualizado con éxito, la estacion y el usuario %s', $stationUser->station_id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StationUser $stationUser)
    {
        $stationUser->delete();

        return redirect()->back()->with('success', sprintf('Eliminado con éxito, la estacion y el usuario %s', $stationUser->station_id));
    }

    protected function setDataStore($request)
    {
        $current_date = $this->getCurrentDate()->format('Y-m-d H.i:s');

        return [
            'station_id' => $request->get('station_id'),
            'user_id' => $request->get('user_id'),
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
            'station_id' => [
                'field' => 'station_id',
                'operator' => 'equal'
            ],
            'user_id' => [
                'field' => 'user_id',
                'operator' => 'equal'
            ]
        ];

        return $fieldList[$field] ?? null;
    }
}
