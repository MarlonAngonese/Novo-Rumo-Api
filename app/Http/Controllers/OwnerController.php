<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\Property;
use App\Models\PropertyAgriculturalMachine;
use App\Models\PropertyVehicle;
use App\Models\Request as ModelsRequest;
use App\Models\UserVisit;
use App\Models\Visit;
use Illuminate\Http\Request;

class OwnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Owner::query();

        // Implements search by name and email
        if ($search = $request->input('search')) {
            $query->where('name', 'regexp', "/.*$search/i");
        }

        // Implements order by name
        $query->orderBy('name', $request->input('sort', 'asc'));

        // Implements mongodb pagination
        $elementsPerPage = 25;
        $page = $request->input('page', 1);
        $total = $query->count();

        $result = $query->offset(($page - 1) * $elementsPerPage)->limit($elementsPerPage)->get();

        return [
            'owners' => $result,
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
        $owner = new Owner($request->input());

        $owner->save();

        return response()->json([
            'owner' => $owner,
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
        $owner = Owner::find($id);

        return response()->json([
            'owner' => $owner,
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
        $owner = Owner::find($id);
        $owner->update($request->all());

        $owner->save();

        return response()->json([
            'owner' => $owner,
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
        Owner::find($id)->delete();

        $properties = Property::query()->where('fk_owner_id', '=', $id)->get();
        foreach ($properties as $property) {
            $property_vehicles = PropertyVehicle::query()->where('fk_property_id', '=', $property->_id)->get();
            $property_agricultural_machines = PropertyAgriculturalMachine::query()->where('fk_property_id', '=', $property->_id)->get();
            $requests = ModelsRequest::query()->where('fk_property_id', '=', $property->_id)->get();
            $visits = Visit::query()->where('fk_property_id', '=', $property->_id)->get();

            foreach($property_vehicles as $property_vehicle) {
                PropertyVehicle::find($property_vehicle->_id)->delete();
            }
            foreach($property_agricultural_machines as $property_agricultural_machine) {
                PropertyAgriculturalMachine::find($property_agricultural_machine->_id)->delete();
            }
            foreach($requests as $request) {
                ModelsRequest::find($request->_id)->delete();
            }
            foreach($visits as $visit) {
                $user_visits = UserVisit::query()->where('fk_visit_id', '=', $visit->_id)->get();
                foreach ($user_visits as $user_visit) {
                    UserVisit::find($user_visit->_id)->delete();
                }
                Visit::find($visit->_id)->delete();
            }

            Property::find($property->_id)->delete();
        }

        return response()->json([
            'deleted' => true,
        ], 204);
    }

    /**
     * Return all owners names
     * 
     * @return Owner $owners
     */
    public function names() {
        $owners = Owner::query()->get(["_id", "firstname", "lastname"]);

        return response()->json([
            'owners' => $owners,
        ], 200);
    }
}
