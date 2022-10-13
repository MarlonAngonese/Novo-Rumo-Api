<?php

namespace App\Http\Controllers;

use App\Models\Garbage;
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
}