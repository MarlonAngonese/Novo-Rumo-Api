<?php

namespace App\Http\Controllers;

use App\Models\Garbage;
use App\Models\PropertyVehicle;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Vehicle::query();

        // Implements search by name and email
        if ($search = $request->input('search')) {
            $query->where('name', 'regexp', "/.*$search/i")
                ->orWhere('brand', 'regexp', "/.*$search/i");
        }

        // Implements order by name
        $query->orderBy('name', $request->input('sort', 'asc'));

        // Implements mongodb pagination
        $elementsPerPage = 25;
        $page = $request->input('page', 1);
        $total = $query->count();

        $result = $query->offset(($page - 1) * $elementsPerPage)->limit($elementsPerPage)->get();

        return [
            'vehicles' => $result,
            'total' => $total,
            'page' => $page,
            'last_page' => ceil($total / $elementsPerPage),
            'search' => $search,
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $vehicle = new Vehicle($request->input());

        $vehicle->save();

        return response()->json([
            'vehicle' => $vehicle,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $vehicle = Vehicle::find($id);

        return response()->json([
            'vehicle' => $vehicle,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::find($id);
        $vehicle->update($request->all());

        $vehicle->save();

        return response()->json([
            'vehicle' => $vehicle,
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = Garbage::new([
            'table' => 'vehicles',
            'deleted_id' => $id,
        ]);
        $deleted->save();

        Vehicle::find($id)->delete();

        $property_vehicles = PropertyVehicle::query()->where('fk_vehicle_id', '=', $id)->get();
        foreach($property_vehicles as $property_vehicle) {
            $deleted = Garbage::new([
                'table' => 'property_vehicles',
                'deleted_id' => $property_vehicle->_id,
            ]);
            $deleted->save();

            PropertyVehicle::find($property_vehicle->_id)->delete();
        }

        return response()->json([
            'deleted' => true,
        ], 204);
    }

    /**
     * Return all vehicle names
     * 
     * @return Vehicle $vehicles
     */
    public function names() {
        $vehicles = Vehicle::query()->get(["_id", "name", "brand"]);

        return response()->json([
            'vehicles' => $vehicles,
        ], 200);
    }
}
