<?php

namespace Modules\Ticket\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Ticket\Models\StationProduct;
use Modules\Ticket\Http\Requests\StationProductRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use Modules\Ticket\Traits\SetFilterQuery;

class StationProductController extends Controller
{
    use SetFilterQuery;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DB::table('v_station_Products')->when($request->get('search'), function ($query, $search) {
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
            ->select('station_product_id', 'station_name', 'product_name', 'status')
            ->paginate($request->get('limit', 10));

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return Inertia::render('Ticket/StationProduct/Index', [
            'result' => $result
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Ticket/StationProduct/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StationProductRequest $request)
    {
        $stationProduct = $this->setDataStore($request);
        $stationProductId = DB::table('station_products ')->insertGetId($stationProduct);

        $message = sprintf('La estacion y el producto %s, ha sido ingresada exitosamente.', $stationProduct['station_id']);

        if ($request->expectsJson()) {
            if ($stationProductId) {
                $newStationProduct = (object) $stationProduct;
                $newStationProduct->stationProduct_id = $stationProductId;
            }

            return response()->json([
                'message' => $message,
                'stationProduct' => $newStationProduct
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StationProduct $stationProduct): Response
    {
        return Inertia::render('Ticket/StationProduct/Edit', [
            'stationProduct' => $stationProduct
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StationProductRequest $request, StationProduct $stationProduct)
    {
        $stationProduct->update($request->all());

        return redirect()->back()->with('success', sprintf('Actualizado con éxito, la estacion y el producto %s', $stationProduct->station_id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StationProduct $stationProduct)
    {
        $stationProduct->delete();

        return redirect()->back()->with('success', sprintf('Eliminado con éxito, la estacion y el producto %s', $stationProduct->station_id));
    }

    protected function setDataStore($request)
    {
        $user = Auth::user();
        $current_date = $this->getCurrentDate()->format('Y-m-d H.i:s');

        return [
            'station_id' => $request->get('station_id'),
            'product_id' => $request->get('product_id'),
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
            'station_id' => [
                'field' => 'station_id',
                'operator' => 'equal'
            ],
            'product_id' => [
                'field' => 'product_id',
                'operator' => 'equal'
            ],
            'status' => [
                'field' => 'status',
                'operator' => 'equal'
            ]
        ];

        return $fieldList[$field] ?? null;
    }
}
