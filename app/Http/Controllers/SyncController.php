<?php

namespace App\Http\Controllers;

use App\Models\AgriculturalMachine;
use App\Models\Garbage;
use App\Models\Owner;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use App\Models\Vehicle;
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
                $usersDeleted = $json['deleted'];

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

                foreach ($usersDeleted as $uD) {
                    $userDeleted = User::find($uD);
                    $userDeleted->delete();
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
                $ownersDeleted = $json['deleted'];

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
                    $ownerData->created_at = new Carbon($owner["createdAt"]);
                    $ownerData->updated_at = new Carbon($owner["updatedAt"]);

                    $ownerData->save();
                }

                foreach ($ownersDeleted as $oD) {
                    $ownersDeleted = Owner::find($oD);
                    $ownersDeleted->delete();
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
                $propertiesDeleted = $json['deleted'];

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
                    $propertyData->created_at = new Carbon($property["createdAt"]);
                    $propertyData->updated_at = new Carbon($property["updatedAt"]);

                    $propertyData->save();
                }

                foreach ($propertiesDeleted as $uD) {
                    $propertyDeleted = Property::find($uD);
                    $propertyDeleted->delete();
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
}