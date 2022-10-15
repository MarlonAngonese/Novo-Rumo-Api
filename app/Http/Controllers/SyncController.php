<?php

namespace App\Http\Controllers;

use App\Models\Garbage;
use App\Models\Owner;
use App\Models\PropertyType;
use App\Models\User;
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
}