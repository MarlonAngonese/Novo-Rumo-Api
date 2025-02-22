<?php

namespace App\Http\Controllers;

use App\Models\AgriculturalMachine;
use App\Models\Garbage;
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

        $hasSearch = $request->input('filterby', false);
        $value = $request->input('value', false);

        if (!$value) {
            $value = $request->input('value_dropdown', false);
            $value = $value == 'yes';
        }

        if ($hasSearch) {
            if ($hasSearch == 'code') {
                $propertiesByCode = Property::query()->where('code', 'regexp', "/.*$value/i")->get();

                foreach ($propertiesByCode as $propertyByCode) {
                    if (!in_array($propertyByCode->_id, $properties_list, true)) {
                        array_push($properties_list, $propertyByCode->_id);
                    }
                }
            } else if ($hasSearch == 'has_geo_board') {
                if ($value) {
                    $propertiesByCode = Property::query()->where('has_geo_board', '=', 1)->orWhere('has_geo_board', '=', "true")->orWhere('has_geo_board', '=', true)->get();
                } else {
                    $propertiesByCode = Property::query()->where('has_geo_board', '=', 0)->orWhere('has_geo_board', '=', "false")->orWhere('has_geo_board', '=', false)->get();
                }

                foreach ($propertiesByCode as $propertyByCode) {
                    if (!in_array($propertyByCode->_id, $properties_list, true)) {
                        array_push($properties_list, $propertyByCode->_id);
                    }
                }
            } else if ($hasSearch == 'has_cams') {
                if ($value) {
                    $propertiesByCode = Property::query()->where('has_cams', '=', 1)->orWhere('has_cams', '=', "true")->orWhere('has_cams', '=', true)->get();
                } else {
                    $propertiesByCode = Property::query()->where('has_cams', '=', 0)->orWhere('has_cams', '=', "false")->orWhere('has_cams', '=', false)->get();
                }

                foreach ($propertiesByCode as $propertyByCode) {
                    if (!in_array($propertyByCode->_id, $properties_list, true)) {
                        array_push($properties_list, $propertyByCode->_id);
                    }
                }
            } else if ($hasSearch == 'has_gun') {
                if ($value) {
                    $propertiesByCode = Property::query()->where('has_gun', '=', 1)->orWhere('has_gun', '=', "true")->orWhere('has_gun', '=', true)->get();
                } else {
                    $propertiesByCode = Property::query()->where('has_gun', '=', 0)->orWhere('has_gun', '=', "false")->orWhere('has_gun', '=', false)->get();
                }

                foreach ($propertiesByCode as $propertyByCode) {
                    if (!in_array($propertyByCode->_id, $properties_list, true)) {
                        array_push($properties_list, $propertyByCode->_id);
                    }
                }
            } else if ($hasSearch == 'qty_agricultural_defensives') {
                if ($value) {
                    $propertiesByCode = Property::query()->where('qty_agricultural_defensives', '>', 0)->get();
                } else {
                    $propertiesByCode = Property::query()->where('qty_agricultural_defensives', '=', 0)->get();
                }

                foreach ($propertiesByCode as $propertyByCode) {
                    if (!in_array($propertyByCode->_id, $properties_list, true)) {
                        array_push($properties_list, $propertyByCode->_id);
                    }
                }
            } else if ($hasSearch == 'owner') {
                $queryOwner = Owner::query();

                $valueExploded = explode(' ', $value);
                $firstValue = $valueExploded[0];
                array_shift($valueExploded);
                $value = implode(" ", $valueExploded);

                if ($value == "") {
                    $queryOwner->where('firstname', 'regexp', "/.*$firstValue/i")->orWhere('lastname', 'regexp', "/.*$firstValue/i");
                } else {
                    $queryOwner->where('firstname', 'regexp', "/.*$firstValue/i")->orWhere('lastname', 'regexp', "/.*$firstValue/i")->orWhere('lastname', 'regexp', "/.*$value/i")->orWhere('firstname', 'regexp', "/.*$value/i");
                }

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
            } else if ($hasSearch == 'cpf') {
                $queryOwner = Owner::query();
                $queryOwner->where('cpf', 'regexp', "/.*$value/i");

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
            } else if ($hasSearch == 'property_type_id') {
                $propertyTypes = PropertyType::query();
                $propertyTypes = $propertyTypes->where('name', 'regexp', "/.*$value/i")->get();

                foreach ($propertyTypes as $propertyType) {
                    $propertiesByCode = Property::query()->where('fk_property_type_id', '=', $propertyType->_id)->get();
    
                    foreach ($propertiesByCode as $propertyByCode) {
                        if (!in_array($propertyByCode->_id, $properties_list, true)) {
                            array_push($properties_list, $propertyByCode->_id);
                        }
                    }
                }
            } else if ($hasSearch == 'car_identification') {
                $propertyVehicles = PropertyVehicle::query();
                $propertyVehicles = $propertyVehicles->where('identification', '=', $value)->get();

                foreach ($propertyVehicles as $propertyVehicle) {
                    $propertiesByCode = Property::query()->where('_id', '=', $propertyVehicle->fk_property_id)->get();
    
                    foreach ($propertiesByCode as $propertyByCode) {
                        if (!in_array($propertyByCode->_id, $properties_list, true)) {
                            array_push($properties_list, $propertyByCode->_id);
                        }
                    }
                }
            }
        }

        // Search By Property Type
        if ($searchPropertyType = $request->input('tp')) {
            $propertiesByCode = Property::query()->where('fk_property_type_id', 'regexp', "/.*$searchPropertyType/i")->get();

            foreach ($propertiesByCode as $propertyByCode) {
                if (!in_array($propertyByCode->_id, $properties_list, true)) {
                    array_push($properties_list, $propertyByCode->_id);
                }
            }
        }

        if ($hasSearch) {
            $properties->whereIn("_id", $properties_list);
        }

        $field = $request->input('column', 'code');
        $sort = $request->input('sort', 'asc');
        $properties->orderBy($field, $sort);

        // Implements mongodb pagination
        $elementsPerPage = 25;
        $page = $request->input('page', 1);
        $total = $properties->count();

        $properties = $properties->offset(($page - 1) * $elementsPerPage)->limit($elementsPerPage)->get(["_id", "code", "fk_owner_id"]);

        foreach ($properties as $property_key => $property) {
            // List Owner
            $owner = Owner::query()->where('_id', '=', $property->fk_owner_id)->get(["_id", "firstname", "lastname"])->first();
            $properties[$property_key]->owner = $owner;
        }

        // Implements order by name
        if ($request->input('column') == 'firstname') {
            $properties = json_encode($properties);
            $properties = json_decode($properties, true);

            if ($request->input('sort') == 'desc' || $request->input('sort') == 'DESC') {
                usort($properties, function ($a, $b) {
                    return $a['owner']['firstname'] < $b['owner']['firstname'];
                });
            } else {
                usort($properties, function ($a, $b) {
                    return $a['owner']['firstname'] <=> $b['owner']['firstname'];
                });
            }
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
        $validator = Validator::make($request->all(), [
            'fk_owner_id' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'qty_people' => 'required',
            'qty_agricultural_defensives' => 'required',
        ], [
            'fk_owner_id.required' => 'Por favor, preencha o proprietário!',
            'latitude.required' => 'Por favor, preencha a latitude!',
            'latitude.required' => 'Por favor, preencha a longitude!',
            'qty_people.required' => 'Por favor, preencha o número de residentes!',
            'qty_agricultural_defensives.required' => 'Por favor, preencha o número de defensivos agrícolas!',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 400);
        }

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
            $vehicle->identification = $property_vehicle->identification;
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

        $owners = Owner::query()->orderBy('firstname', 'asc')->get(["_id", "firstname", "lastname"]);
        $property_types = PropertyType::query()->orderBy('name', 'asc')->get(["_id", "name"]);
        $agricultural_machines = AgriculturalMachine::query()->get(["_id", "name"]);
        $vehicles = Vehicle::query()->get(["_id", "name", "brand"]);

        return response()->json([
            'property' => $property,
            'owners' => $owners,
            'agricultural_machines' => $agricultural_machines,
            'vehicles' => $vehicles,
            'property_types' => $property_types,
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

        // return ["breakpoint"];

        $validator = Validator::make($request->all(), [
            'fk_owner_id' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'qty_people' => 'required',
            'qty_agricultural_defensives' => 'required',
        ], [
            'fk_owner_id.required' => 'Por favor, preencha o proprietário!',
            'latitude.required' => 'Por favor, preencha a latitude!',
            'longitude.required' => 'Por favor, preencha a longitude!',
            'qty_people.required' => 'Por favor, preencha o número de residentes!',
            'qty_agricultural_defensives.required' => 'Por favor, preencha o número de defensivos agrícolas!',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 400);
        }

        $property = Property::find($id);

        // Save visits
        $property->update($request->all());
        $property->save();

        $property_vehicles = PropertyVehicle::query()->where('fk_property_id', '=', $property->_id)->get();

        $property_agricultural_machines = PropertyAgriculturalMachine::query()->where('fk_property_id', '=', $property->_id)->get();

        foreach($property_agricultural_machines as $property_agricultural_machine) {
            $deleted = new Garbage([
                'table' => 'property_agricultural_machines',
                'deleted_id' => $property_agricultural_machine->_id,
            ]);
            $deleted->save();

            PropertyAgriculturalMachine::find($property_agricultural_machine->_id)->delete();
        }

        
        if ($vehicles = $request->input('vehicles')) {
            // return $vehicles;    
            $property_vehicles = PropertyVehicle::query()->where('fk_property_id', '=', $id)->get();

            // List property vehicles, change if necessary. If count of current vehicles passes, delete it
            foreach ($property_vehicles as $property_vehicle) {
                if (!empty($vehicles)) {
                    $current_vehicle = array_shift($vehicles);

                    $property_vehicle->fk_vehicle_id = $current_vehicle["id"];
                    $property_vehicle->color = $current_vehicle["color"];
                    $property_vehicle->identification = $current_vehicle["identification"];

                    $property_vehicle->save();

                    // return [$property_vehicle, $current_vehicle, $vehicles];
                } else {
                    $property_vehicle->delete();
                }
            }

            // Verify if request has more vehicles than the current database
            if (!empty($vehicles)) {
                foreach ($vehicles as $vehicle) {
                    $property_vehicle = new PropertyVehicle([
                        "fk_vehicle_id" => $vehicle["id"],
                        "color" => $vehicle["color"],
                        "identification" => $vehicle["identification"],
                        "fk_property_id" => $id,
                    ]);

                    $property_vehicle->save();
                }
            }
        } else {
            $property_vehicles = PropertyVehicle::query()->where('fk_property_id', '=', $id)->get();

            // List property vehicles, change if necessary. If count of current vehicles passes, delete it
            foreach ($property_vehicles as $property_vehicle) {
                $property_vehicle->delete();
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
        $deleted = new Garbage([
            'table' => 'properties',
            'deleted_id' => $id,
        ]);
        $deleted->save();

        Property::find($id)->delete();

        $property_vehicles = PropertyVehicle::query()->where('fk_property_id', '=', $id)->get();
        $property_agricultural_machines = PropertyAgriculturalMachine::query()->where('fk_property_id', '=', $id)->get();
        $requests = ModelsRequest::query()->where('fk_property_id', '=', $id)->get();
        $visits = Visit::query()->where('fk_property_id', '=', $id)->get();

        foreach($property_vehicles as $property_vehicle) {
            $deleted = new Garbage([
                'table' => 'property_vehicles',
                'deleted_id' => $property_vehicle->_id,
            ]);
            $deleted->save();

            PropertyVehicle::find($property_vehicle->_id)->delete();
        }
        foreach($property_agricultural_machines as $property_agricultural_machine) {
            $deleted = new Garbage([
                'table' => 'property_agricultural_machines',
                'deleted_id' => $property_agricultural_machine->_id,
            ]);
            $deleted->save();

            PropertyAgriculturalMachine::find($property_agricultural_machine->_id)->delete();
        }
        foreach($requests as $request) {
            $deleted = new Garbage([
                'table' => 'requests',
                'deleted_id' => $request->_id,
            ]);
            $deleted->save();

            ModelsRequest::find($request->_id)->delete();
        }
        foreach($visits as $visit) {
            $user_visits = UserVisit::query()->where('fk_visit_id', '=', $visit->_id)->get();
            foreach ($user_visits as $user_visit) {
                $deleted = new Garbage([
                    'table' => 'user_visits',
                    'deleted_id' => $user_visit->_id,
                ]);
                $deleted->save();

                UserVisit::find($user_visit->_id)->delete();
            }
            $deleted = new Garbage([
                'table' => 'visits',
                'deleted_id' => $visit->_id,
            ]);
            $deleted->save();

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
        $properties = Property::query()->orderBy('code', 'asc')->where('code', "!=", null)->get(["_id", "code"]);
        $users = User::query()->orderBy('name', 'asc')->get(['_id', 'name']);

        return response()->json([
            'properties' => $properties,
            'users' => $users,
        ], 200);
    }
    
}
