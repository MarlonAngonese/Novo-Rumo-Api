<?php

namespace App\Http\Controllers;

use App\Models\AgriculturalMachine;
use App\Models\Owner;
use App\Models\Property;
use App\Models\PropertyAgriculturalMachine;
use App\Models\PropertyType;
use App\Models\PropertyVehicle;
use App\Models\PropertyVisit;
use App\Models\Request as ModelsRequest;
use App\Models\User;
use App\Models\UserVisit;
use App\Models\Vehicle;
use App\Models\Visit;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $properties = Property::query();

        $properties_list = [];

        $hasSearch = $request->input('o') || $request->input('c');

        /** Search **/
        // Search By Owner
        if ($searchOwner = $request->input('o')) {
            $queryOwner = Owner::query();

            $queryOwner->where('_id', '=', $searchOwner);

            $owners = $queryOwner->get();

            $owners_ids = [];

            foreach ($owners as $owner) {
                array_push($owners_ids, $owner->_id);
            }

            $owner_properties = Property::whereIn('fk_owner_id', $owners_ids)->get();

            foreach ($owner_properties as $owner_property) {
                if (!in_array($owner_property->_id, $properties_list, true)) {
                    array_push($properties_list, $owner_property->_id);
                }
            }
        }

        // Search By Code
        if ($searchProperty = $request->input('c')) {
            $propertiesByCode = Property::query()->where('code', 'regexp', "/.*$searchProperty/i")->get();

            foreach ($propertiesByCode as $propertyByCode) {
                if (!in_array($propertyByCode->_id, $properties_list, true)) {
                    array_push($properties_list, $propertyByCode->_id);
                }
            }
        }

        if ($hasSearch) $properties->whereIn('_id', $properties_list);
        
        // Implements order by name
        $properties->orderBy('updated_at', $request->input('sort', 'desc'));

        // Implements mongodb pagination
        $elementsPerPage = 25;
        $page = $request->input('page', 1);
        $total = $properties->count();

        $properties = $properties->offset(($page - 1) * $elementsPerPage)->limit($elementsPerPage)->get();

        $vehicles = [];
        $agricultural_machines = [];
        $visits = [];
        foreach ($properties as $property_key => $property) {
            // List Owner
            $owner = Owner::query()->where('_id', '=', $property->fk_owner_id)->first();
            $properties[$property_key]->owner = $owner;

            // List Property Type
            $property_type = PropertyType::query()->where('_id', '=', $property->fk_property_type_id)->first();
            $properties[$property_key]->property_type = $property_type;

            // List Property Vehicles
            $property_vehicles = PropertyVehicle::query()->where('fk_property_id', '=', $property->_id)->get();
            foreach($property_vehicles as $property_vehicle) {
                $vehicle = Vehicle::query()->where('_id', '=', $property_vehicle->fk_vehicle_id)->first();
                $vehicle->color = $property_vehicle->color;
                array_push($vehicles, $vehicle);
            }
            $properties[$property_key]->vehicles = $vehicles;
            $vehicles = [];

            // List Agricultural Machines
            $property_agricultural_machines = PropertyAgriculturalMachine::query()->where('fk_property_id', '=', $property->_id)->get();
            foreach($property_agricultural_machines as $property_agricultural_machine) {
                $agricultural_machine = AgriculturalMachine::query()->where('_id', '=', $property_agricultural_machine->fk_agricultural_machine_id)->first();
                array_push($agricultural_machines, $agricultural_machine);
            }
            $properties[$property_key]->agricultural_machines = $agricultural_machines;
            $agricultural_machines = [];

            // List Requests
            $model_requests = ModelsRequest::query()->where('fk_property_id', '=', $property->_id)->get();
            $properties[$property_key]->requests = $model_requests;

            // List Property Visits
            $property_visits = Visit::query()->where('fk_property_id', '=', $property->_id)->get();
            foreach($property_visits as $property_visit) {
                $user_visits = UserVisit::query()->where('fk_visit_id', $property_visit->_id)->get();
                $users = [];
                foreach ($user_visits as $user_visit) {
                    $user = User::query()->where('_id', '=', $user_visit->fk_user_id)->get();
                    array_push($users, $user);
                }
                $property_visit->users = $users;
                array_push($visits, $property_visit);
            }

            $properties[$property_key]->visits = $visits;
            $visits = [];
        }

        return [
            'properties' => $properties,
            'total' => $total,
            'page' => $page,
            'last_page' => ceil($total / $elementsPerPage)
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
        try {
            $property = new Property($request->all());
            $property->save();
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao salvar a propriedade'
            ], 400);
        }

        if ($vehicles = $request->input('vehicles')) {
            foreach ($vehicles as $vehicle) {
                $property_vehicle = new PropertyVehicle([
                    'fk_property_id' => $property->_id,
                    'fk_vehicle_id' => $vehicle["id"],
                    'color' => $vehicle["color"],
                ]);

                $property_vehicle->save();
            }
        }

        if ($agricultural_machines = $request->input('agricultural_machines')) {
            foreach ($agricultural_machines as $agricultural_machine_id) {
                $property_agricultural_machine = new PropertyAgriculturalMachine([
                    'fk_property_id' => $property->_id,
                    'fk_agricultural_machine_id' => $agricultural_machine_id,
                ]);

                $property_agricultural_machine->save();
            }
        }

        return response()->json([
            'property' => $property,
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
        $property = Property::find($id);

        // List Owner
        $owner = Owner::query()->where('_id', '=', $property->fk_owner_id)->first();
        $property->owner = $owner;

        // List Property Type
        $property_type = PropertyType::query()->where('_id', '=', $property->fk_property_type_id)->first();
        $property->property_type = $property_type;

        // List Property Vehicles
        $vehicles = [];
        $property_vehicles = PropertyVehicle::query()->where('fk_property_id', '=', $property->_id)->get();
        foreach($property_vehicles as $property_vehicle) {
            $vehicle = Vehicle::query()->where('_id', '=', $property_vehicle->fk_vehicle_id)->first();
            $vehicle->color = $property_vehicle->color;
            array_push($vehicles, $vehicle);
        }
        $property->vehicles = $vehicles;

        // List Agricultural Machines
        $agricultural_machines = [];
        $property_agricultural_machines = PropertyAgriculturalMachine::query()->where('fk_property_id', '=', $property->_id)->get();
        foreach($property_agricultural_machines as $property_agricultural_machine) {
            $agricultural_machine = AgriculturalMachine::query()->where('_id', '=', $property_agricultural_machine->fk_agricultural_machine_id)->first();
            array_push($agricultural_machines, $agricultural_machine);
        }
        $property->agricultural_machines = $agricultural_machines;

        // List Requests
        $model_requests = ModelsRequest::query()->where('fk_property_id', '=', $property->_id)->get();
        $property->requests = $model_requests;

        // List Property Visits
        $visits = [];
        $property_visits = Visit::query()->where('fk_property_id', '=', $property->_id)->get();
        foreach($property_visits as $property_visit) {
            $user_visits = UserVisit::query()->where('fk_visit_id', $property_visit->_id)->get();
            $users = [];
            foreach ($user_visits as $user_visit) {
                $user = User::query()->where('_id', '=', $user_visit->fk_user_id)->get();
                array_push($users, $user);
            }
            $property_visit->users = $users;
            array_push($visits, $property_visit);
        }
        $property->visits = $visits;

        return response()->json([
            'property' => $property,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $property = Property::find($id);


        // Save visits
        $property->update($request->all());
        $property->save();

        $property_vehicles = PropertyVehicle::query()->where('fk_property_id', '=', $property->_id)->get();

        foreach($property_vehicles as $property_vehicle) {
            PropertyVehicle::find($property_vehicle->_id)->delete();
        }

        $property_agricultural_machines = PropertyAgriculturalMachine::query()->where('fk_property_id', '=', $property->_id)->get();

        foreach($property_agricultural_machines as $property_agricultural_machine) {
            PropertyAgriculturalMachine::find($property_agricultural_machine->_id)->delete();
        }

        if ($vehicles = $request->input('vehicles')) {
            foreach ($vehicles as $vehicle) {
                $property_vehicle = new PropertyVehicle([
                    'fk_property_id' => $property->_id,
                    'fk_vehicle_id' => $vehicle["id"],
                    'color' => $vehicle["color"],
                ]);

                $property_vehicle->save();
            }
        }

        if ($agricultural_machines = $request->input('agricultural_machines')) {
            foreach ($agricultural_machines as $agricultural_machine_id) {
                $property_agricultural_machine = new PropertyAgriculturalMachine([
                    'fk_property_id' => $property->_id,
                    'fk_agricultural_machine_id' => $agricultural_machine_id,
                ]);

                $property_agricultural_machine->save();
            }
        }

        return response()->json([
            'property' => $property,
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
        Property::find($id)->delete();

        $property_vehicles = PropertyVehicle::query()->where('fk_property_id', '=', $id)->get();
        $property_agricultural_machines = PropertyAgriculturalMachine::query()->where('fk_property_id', '=', $id)->get();
        $requests = ModelsRequest::query()->where('fk_property_id', '=', $id)->get();
        $visits = Visit::query()->where('fk_property_id', '=', $id)->get();

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

        return response()->json([
            'deleted' => true,
        ], 204);
    }

    /**
     * Return Property Codes
     * 
     * @return Property properties
     */
    public function codes() {
        $properties = Property::query()->where('code', "!=", null)->get(["_id", "code"]);
        $users = User::query()->get(['_id', 'name']);

        return response()->json([
            'properties' => $properties,
            'users' => $users,
        ], 200);
    }
    
}
