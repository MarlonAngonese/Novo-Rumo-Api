<?php

namespace App\Http\Controllers;

use App\Models\AgriculturalMachine;
use App\Models\Garbage;
use App\Models\Owner;
use App\Models\Property;
use App\Models\PropertyAgriculturalMachine;
use App\Models\PropertyType;
use App\Models\PropertyVehicle;
use App\Models\Request as ModelsRequest;
use App\Models\User;
use App\Models\UserVisit;
use App\Models\Vehicle;
use App\Models\Visit;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use MongoDB\BSON\ObjectId;

class SyncController extends Controller {
    /**
     * Send Users data
     * 
     * @param Request $request request data
     */
    public function syncUsers(Request $request) {

        try {
            if ($request->method() == "POST") {
                
                $json = json_decode($request->getContent(), true);
                $users = $json['users'];

                foreach ($users as $user) {
                    $existingUser = User::find($user["_id"]);

                    if (!$existingUser) { // New User
                        $userData = new User;
                    } else { // User exists
                        $userData = $existingUser;
                    }
                    
                    $userData->_id = new ObjectId($user["_id"]);
                    $userData->name = $user["name"];
                    $userData->email = $user["email"];
                    $userData->password = $user["password"];
                    $userData->created_at = new Carbon($user["createdAt"]);
                    $userData->updated_at = new Carbon($user["updatedAt"]);

                    $userData->save();
                }

                return response()->json([
                    'updated' => true,
                ], 201);
            }
    
            // Check for last_date request param
            if (isset($request["last_date"])) {
                $last_date = date('Y-m-d H:i:s', strtotime($request["last_date"]));
                $userQuery = User::query()->where('updated_at', '>', new DateTime($last_date));
    
                $deletedQuery = Garbage::query()->where('table', '=', 'users')->where('updated_at', '>', new DateTime($last_date));
                return response()->json(
                    [
                        'users' => $userQuery->get(),
                        'deleted' => $deletedQuery->get(),
                    ]
                );
            }
    
            // If there isn't any last_date param, return all data
            return response()->json(User::query()->get());
        } catch (Exception $e) {
            return response()->json([ 'error' => strval($e) ], 500);
        }
    }

    /**
     * Send Owners data
     * 
     * @param Request $request request data
     */
    public function syncOwners(Request $request) {

        try {
            if ($request->method() == "POST") {
                
                $json = json_decode($request->getContent(), true);
                $owners = $json['owners'];

                foreach ($owners as $owner) {
                    $existingOwner = Owner::find($owner["_id"]);

                    if (!$existingOwner) { // New Owner
                        $ownerData = new Owner;
                    } else { // Owner exists
                        $ownerData = $existingOwner;
                    }
                    
                    $ownerData->_id = new ObjectId($owner["_id"]);
                    $ownerData->firstname = $owner["firstname"];
                    $ownerData->lastname = $owner["lastname"];
                    $ownerData->cpf = $owner["cpf"];
                    $ownerData->phone1 = $owner["phone1"];
                    $ownerData->phone2 = $owner["phone2"];
                    $ownerData->created_at = new Carbon($owner["createdAt"]);
                    $ownerData->updated_at = new Carbon($owner["updatedAt"]);

                    $ownerData->save();
                }

                return response()->json([
                    'updated' => true,
                ], 201);
            }
    
            // Check for last_date request param
            if (isset($request["last_date"])) {
                $last_date = date('Y-m-d H:i:s', strtotime($request["last_date"]));
                $ownerQuery = Owner::query()->where('updated_at', '>', new DateTime($last_date));
    
                $deletedQuery = Garbage::query()->where('table', '=', 'owners')->where('updated_at', '>', new DateTime($last_date));
                return response()->json(
                    [
                        'owners' => $ownerQuery->get(),
                        'deleted' => $deletedQuery->get(),
                    ]
                );
            }
    
            // If there isn't any last_date param, return all data
            return response()->json(Owner::query()->get());
        } catch (Exception $e) {
            return response()->json([ 'error' => strval($e) ], 500);
        }
    }

    /**
     * Send PropertyTypes data
     * 
     * @param Request $request request data
     */
    public function syncPropertyTypes(Request $request) {

        try {
            // Check for last_date request param
            if (isset($request["last_date"])) {
                $last_date = date('Y-m-d H:i:s', strtotime($request["last_date"]));
                $propertyTypeQuery = PropertyType::query()->where('updated_at', '>', new DateTime($last_date));
    
                $deletedQuery = Garbage::query()->where('table', '=', 'property_types')->where('updated_at', '>', new DateTime($last_date));
                return response()->json(
                    [
                        'propertyTypes' => $propertyTypeQuery->get(),
                        'deleted' => $deletedQuery->get(),
                    ]
                );
            }
    
            // If there isn't any last_date param, return all data
            return response()->json(PropertyType::query()->get());
        } catch (Exception $e) {
            return response()->json([ 'error' => strval($e) ], 500);
        }
    }

    /**
     * Send Properties data
     * 
     * @param Request $request request data
     */
    public function syncProperties(Request $request) {

        try {
            if ($request->method() == "POST") {
                
                $json = json_decode($request->getContent(), true);
                $properties = $json['properties'];

                foreach ($properties as $property) {
                    $existingProperty = Property::find($property["_id"]);

                    if (!$existingProperty) { // New Property
                        $propertyData = new Property;
                    } else { // Property exists
                        $propertyData = $existingProperty;
                    }
                    
                    $propertyData->_id = new ObjectId($property["_id"]);
                    $propertyData->code = $property["code"];
                    $propertyData->has_geo_board = $property["has_geo_board"];
                    $propertyData->qty_people = $property["qty_people"];
                    $propertyData->has_cams = $property["has_cams"];
                    $propertyData->has_phone_signal = $property["has_phone_signal"];
                    $propertyData->has_internet = $property["has_internet"];
                    $propertyData->has_gun = $property["has_gun"];
                    $propertyData->has_gun_local = $property["has_gun_local"];
                    $propertyData->gun_local_description = $property["gun_local_description"];
                    $propertyData->qty_agricultural_defensives = $property["qty_agricultural_defensives"];
                    $propertyData->observations = $property["observations"];
                    $propertyData->fk_owner_id = $property["fk_owner_id"];
                    $propertyData->fk_property_type_id = $property["fk_property_type_id"];
                    $propertyData->latitude = $property["latitude"];
                    $propertyData->longitude = $property["longitude"];
                    $propertyData->area = $property["area"];
                    $propertyData->created_at = new Carbon($property["createdAt"]);
                    $propertyData->updated_at = new Carbon($property["updatedAt"]);

                    $propertyData->save();
                }

                return response()->json([
                    'updated' => true,
                ], 201);
            }
    
            // Check for last_date request param
            if (isset($request["last_date"])) {
                $last_date = date('Y-m-d H:i:s', strtotime($request["last_date"]));
                $propertyQuery = Property::query()->where('updated_at', '>', new DateTime($last_date));
    
                $deletedQuery = Garbage::query()->where('table', '=', 'properties')->where('updated_at', '>', new DateTime($last_date));
                return response()->json(
                    [
                        'properties' => $propertyQuery->get(),
                        'deleted' => $deletedQuery->get(),
                    ]
                );
            }
    
            // If there isn't any last_date param, return all data
            return response()->json(Property::query()->get());
        } catch (Exception $e) {
            return response()->json([ 'error' => strval($e) ], 500);
        }
    }

    /**
     * Send Vehicles data
     * 
     * @param Request $request request data
     */
    public function syncVehicles(Request $request) {

        try {
            if ($request->method() == "POST") {
                
                $json = json_decode($request->getContent(), true);
                $vehicles = $json['vehicles'];

                foreach ($vehicles as $vehicle) {
                    $existingVehicle = Vehicle::find($vehicle["_id"]);

                    if (!$existingVehicle) {
                        $vehicleData = new Vehicle();
                    } else {
                        $vehicleData = $existingVehicle;
                    }
                    
                    $vehicleData->_id = new ObjectId($vehicle["_id"]);
                    $vehicleData->name = $vehicle["name"];
                    $vehicleData->brand = $vehicle["brand"];
                    $vehicleData->created_at = new Carbon($vehicle["createdAt"]);
                    $vehicleData->updated_at = new Carbon($vehicle["updatedAt"]);

                    $vehicleData->save();
                }

                return response()->json([
                    'updated' => true,
                ], 201);
            }
    
            // Check for last_date request param
            if (isset($request["last_date"])) {
                $last_date = date('Y-m-d H:i:s', strtotime($request["last_date"]));
                $vehicleQuery = Vehicle::query()->where('updated_at', '>', new DateTime($last_date));
    
                $deletedQuery = Garbage::query()->where('table', '=', 'vehicles')->where('updated_at', '>', new DateTime($last_date));
                return response()->json(
                    [
                        'vehicles' => $vehicleQuery->get(),
                        'deleted' => $deletedQuery->get(),
                    ]
                );
            }
    
            // If there isn't any last_date param, return all data
            return response()->json(Vehicle::query()->get());
        } catch (Exception $e) {
            return response()->json([ 'error' => strval($e) ], 500);
        }
    }

    /**
     * Send AgriculturalMachine data
     * 
     * @param Request $request request data
     */
    public function syncAgriculturalMachines(Request $request) {

        try {
            if ($request->method() == "POST") {
                
                $json = json_decode($request->getContent(), true);
                $agricultural_machines = $json['agricultural_machines'];

                foreach ($agricultural_machines as $agricultural_machine) {
                    $existingAgriculturalMachine = AgriculturalMachine::find($agricultural_machine["_id"]);

                    if (!$existingAgriculturalMachine) {
                        $agricultural_machineData = new AgriculturalMachine();
                    } else {
                        $agricultural_machineData = $existingAgriculturalMachine;
                    }
                    
                    $agricultural_machineData->_id = new ObjectId($agricultural_machine["_id"]);
                    $agricultural_machineData->name = $agricultural_machine["name"];
                    $agricultural_machineData->created_at = new Carbon($agricultural_machine["createdAt"]);
                    $agricultural_machineData->updated_at = new Carbon($agricultural_machine["updatedAt"]);

                    $agricultural_machineData->save();
                }

                return response()->json([
                    'updated' => true,
                ], 201);
            }
    
            // Check for last_date request param
            if (isset($request["last_date"])) {
                $last_date = date('Y-m-d H:i:s', strtotime($request["last_date"]));
                $agricultural_machineQuery = AgriculturalMachine::query()->where('updated_at', '>', new DateTime($last_date));
    
                $deletedQuery = Garbage::query()->where('table', '=', 'agricultural_machines')->where('updated_at', '>', new DateTime($last_date));
                return response()->json(
                    [
                        'agricultural_machines' => $agricultural_machineQuery->get(),
                        'deleted' => $deletedQuery->get(),
                    ]
                );
            }
    
            // If there isn't any last_date param, return all data
            return response()->json(AgriculturalMachine::query()->get());
        } catch (Exception $e) {
            return response()->json([ 'error' => strval($e) ], 500);
        }
    }

    /**
     * Send Visits data
     * 
     * @param Request $request request data
     */
    public function syncVisits(Request $request) {

        try {
            if ($request->method() == "POST") {
                
                $json = json_decode($request->getContent(), true);
                $visits = $json['visits'];

                foreach ($visits as $visit) {
                    $existingVisit = Visit::find($visit["_id"]);

                    if (!$existingVisit) { // New Visit
                        $visitData = new Visit;
                    } else { // Visit exists
                        $visitData = $existingVisit;
                    }
                    
                    $visitData->_id = new ObjectId($visit["_id"]);
                    $visitData->car = $visit["car"];
                    $visitData->date = new Carbon($visit["date"]);
                    $visitData->fk_property_id = $visit["fk_property_id"];
                    $visitData->history = $visit["history"];
                    $visitData->created_at = new Carbon($visit["createdAt"]);
                    $visitData->updated_at = new Carbon($visit["updatedAt"]);

                    $visitData->save();
                }

                return response()->json([
                    'updated' => true,
                ], 201);
            }
    
            // Check for last_date request param
            if (isset($request["last_date"])) {
                $last_date = date('Y-m-d H:i:s', strtotime($request["last_date"]));
                $visitQuery = Visit::query()->where('updated_at', '>', new DateTime($last_date));
    
                $deletedQuery = Garbage::query()->where('table', '=', 'visits')->where('updated_at', '>', new DateTime($last_date));
                return response()->json(
                    [
                        'visits' => $visitQuery->get(),
                        'deleted' => $deletedQuery->get(),
                    ]
                );
            }
    
            // If there isn't any last_date param, return all data
            return response()->json(Visit::query()->get());
        } catch (Exception $e) {
            return response()->json([ 'error' => strval($e) ], 500);
        }
    }

    /**
     * Send Request data
     * 
     * @param Request $request request data
     */
    public function syncRequests(Request $request) {

        try {
            if ($request->method() == "POST") {
                
                $json = json_decode($request->getContent(), true);
                $model_requests = $json['requests'];

                foreach ($model_requests as $model_request) {
                    $existingModelRequest = ModelsRequest::find($model_request["_id"]);

                    if (!$existingModelRequest) { // New Request
                        $model_requestData = new ModelsRequest;
                    } else { // Request exists
                        $model_requestData = $existingModelRequest;
                    }
                    
                    $model_requestData->_id = new ObjectId($model_request["_id"]);
                    $model_requestData->agency = $model_request["agency"];
                    $model_requestData->has_success = $model_request["has_success"];
                    $model_requestData->fk_property_id = $model_request["fk_property_id"];
                    $model_requestData->created_at = new Carbon($model_request["createdAt"]);
                    $model_requestData->updated_at = new Carbon($model_request["updatedAt"]);

                    $model_requestData->save();
                }

                return response()->json([
                    'updated' => true,
                ], 201);
            }
    
            // Check for last_date request param
            if (isset($request["last_date"])) {
                $last_date = date('Y-m-d H:i:s', strtotime($request["last_date"]));
                $model_requestQuery = ModelsRequest::query()->where('updated_at', '>', new DateTime($last_date));
    
                $deletedQuery = Garbage::query()->where('table', '=', 'requests')->where('updated_at', '>', new DateTime($last_date));
                return response()->json(
                    [
                        'requests' => $model_requestQuery->get(),
                        'deleted' => $deletedQuery->get(),
                    ]
                );
            }
    
            // If there isn't any last_date param, return all data
            return response()->json(ModelsRequest::query()->get());
        } catch (Exception $e) {
            return response()->json([ 'error' => strval($e) ], 500);
        }
    }

    /**
     * Send UserVisits data
     * 
     * @param Request $request request data
     */
    public function syncUserVisits(Request $request) {

        try {
            if ($request->method() == "POST") {
                
                $json = json_decode($request->getContent(), true);
                $user_visits = $json['user_visits'];

                foreach ($user_visits as $user_visit) {
                    $existingUser_visit = UserVisit::find($user_visit["_id"]);

                    if (!$existingUser_visit) { // New User_visit
                        $user_visitData = new UserVisit;
                    } else { // User_visit exists
                        $user_visitData = $existingUser_visit;
                    }
                    
                    $user_visitData->_id = new ObjectId($user_visit["_id"]);
                    $user_visitData->fk_visit_id = $user_visit["fk_visit_id"];
                    $user_visitData->fk_user_id = $user_visit["fk_user_id"];
                    $user_visitData->created_at = new Carbon($user_visit["createdAt"]);
                    $user_visitData->updated_at = new Carbon($user_visit["updatedAt"]);

                    $user_visitData->save();
                }

                return response()->json([
                    'updated' => true,
                ], 201);
            }
    
            // Check for last_date request param
            if (isset($request["last_date"])) {
                $last_date = date('Y-m-d H:i:s', strtotime($request["last_date"]));
                $user_visitQuery = UserVisit::query()->where('updated_at', '>', new DateTime($last_date));
    
                $deletedQuery = Garbage::query()->where('table', '=', 'user_visits')->where('updated_at', '>', new DateTime($last_date));
                return response()->json(
                    [
                        'user_visits' => $user_visitQuery->get(),
                        'deleted' => $deletedQuery->get(),
                    ]
                );
            }
    
            // If there isn't any last_date param, return all data
            return response()->json(UserVisit::query()->get());
        } catch (Exception $e) {
            return response()->json([ 'error' => strval($e) ], 500);
        }
    }

    /**
     * Send PropertyVehicles data
     * 
     * @param Request $request request data
     */
    public function syncPropertyVehicles(Request $request) {

        try {
            if ($request->method() == "POST") {
                
                $json = json_decode($request->getContent(), true);
                $property_vehicles = $json['property_vehicles'];
                $deleted = $json["deleted"];

                foreach ($property_vehicles as $property_vehicle) {
                    $existingProperty_vehicle = PropertyVehicle::find($property_vehicle["_id"]);

                    if (!$existingProperty_vehicle) { // New Property_vehicle
                        $property_vehicleData = new PropertyVehicle;
                    } else { // Property_vehicle exists
                        $property_vehicleData = $existingProperty_vehicle;
                    }
                    
                    $property_vehicleData->_id = new ObjectId($property_vehicle["_id"]);
                    $property_vehicleData->fk_vehicle_id = $property_vehicle["fk_vehicle_id"];
                    $property_vehicleData->fk_property_id = $property_vehicle["fk_property_id"];
                    $property_vehicleData->color = $property_vehicle["color"];
                    $property_vehicleData->identification = $property_vehicle["identification"];
                    $property_vehicleData->created_at = new Carbon($property_vehicle["createdAt"]);
                    $property_vehicleData->updated_at = new Carbon($property_vehicle["updatedAt"]);

                    $property_vehicleData->save();
                }

                foreach ($deleted as $del) {
                    $del = PropertyVehicle::find($del);
                    if ($del) {
                        $del->delete();
                    }
                }

                return response()->json([
                    'updated' => true,
                ], 201);
            }
    
            // Check for last_date request param
            if (isset($request["last_date"])) {
                $last_date = date('Y-m-d H:i:s', strtotime($request["last_date"]));
                $property_vehicleQuery = PropertyVehicle::query()->where('updated_at', '>', new DateTime($last_date));
    
                $deletedQuery = Garbage::query()->where('table', '=', 'property_vehicles')->where('updated_at', '>', new DateTime($last_date));
                return response()->json(
                    [
                        'property_vehicles' => $property_vehicleQuery->get(),
                        'deleted' => $deletedQuery->get(),
                    ]
                );
            }
    
            // If there isn't any last_date param, return all data
            return response()->json(PropertyVehicle::query()->get());
        } catch (Exception $e) {
            return response()->json([ 'error' => strval($e) ], 500);
        }
    }

    /**
     * Send PropertyAgriculturalMachinesdata
     * 
     * @param Request $request request data
     */
    public function syncPropertyAgriculturalMachines(Request $request) {

        try {
            if ($request->method() == "POST") {
                
                $json = json_decode($request->getContent(), true);
                $property_agricultural_machines = $json['property_agricultural_machines'];
                $deleted = $json['deleted'];

                foreach ($property_agricultural_machines as $property_agricultural_machine) {
                    $existingProperty_agricultural_machine = PropertyAgriculturalMachine::find($property_agricultural_machine["_id"]);

                    if (!$existingProperty_agricultural_machine) { // New Property_agricultural_machine
                        $property_agricultural_machineData = new PropertyAgriculturalMachine;
                    } else { // Property_agricultural_machine exists
                        $property_agricultural_machineData = $existingProperty_agricultural_machine;
                    }
                    
                    $property_agricultural_machineData->_id = new ObjectId($property_agricultural_machine["_id"]);
                    $property_agricultural_machineData->fk_agricultural_machine_id = $property_agricultural_machine["fk_agricultural_machine_id"];
                    $property_agricultural_machineData->fk_property_id = $property_agricultural_machine["fk_property_id"];
                    $property_agricultural_machineData->created_at = new Carbon($property_agricultural_machine["createdAt"]);
                    $property_agricultural_machineData->updated_at = new Carbon($property_agricultural_machine["updatedAt"]);

                    $property_agricultural_machineData->save();
                }

                foreach ($deleted as $del) {
                    $del = PropertyAgriculturalMachine::find($del);
                    if ($del) {
                        $del->delete();
                    }
                }

                return response()->json([
                    'updated' => true,
                ], 201);
            }
    
            // Check for last_date request param
            if (isset($request["last_date"])) {
                $last_date = date('Y-m-d H:i:s', strtotime($request["last_date"]));
                $property_agricultural_machineQuery = PropertyAgriculturalMachine::query()->where('updated_at', '>', new DateTime($last_date));
    
                $deletedQuery = Garbage::query()->where('table', '=', 'property_agricultural_machines')->where('updated_at', '>', new DateTime($last_date));
                return response()->json(
                    [
                        'property_agricultural_machines' => $property_agricultural_machineQuery->get(),
                        'deleted' => $deletedQuery->get(),
                    ]
                );
            }
    
            // If there isn't any last_date param, return all data
            return response()->json(PropertyAgriculturalMachine::query()->get());
        } catch (Exception $e) {
            return response()->json([ 'error' => strval($e) ], 500);
        }
    }
}
