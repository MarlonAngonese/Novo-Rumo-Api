<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SyncController extends Controller {

    public function syncUsers() {
        return User::query()->get();
    }
}