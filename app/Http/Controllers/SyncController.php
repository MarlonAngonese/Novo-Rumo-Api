<?php

namespace App\Http\Controllers;

use App\Models\Garbage;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class SyncController extends Controller {
    /**
     * Send Users data
     * 
     * @param Request $request request data
     */
    public function syncUsers(Request $request) {

        // Check for last_date request param
        if (isset($request["last_date"])) {
            $last_date = date('Y-m-d H:i:s', strtotime($request["last_date"]));
            $userQuery = User::query()->where('updated_at', '>', new DateTime($last_date));

            $deletedQuery = Garbage::query()->where('table', '=', 'users')->where('updated_at', '>', new DateTime($last_date));
            
            return [
                'users' => $userQuery->get(),
                'deleted' => $deletedQuery->get(),
            ];
        }

        // If there isn't any last_date param, return all data
        return User::query()->get();
    }
}