<?php

namespace App\Http\Controllers;

use App\Models\Garbage;
use App\Models\Property;
use App\Models\PropertyVisit;
use App\Models\User;
use App\Models\UserVisit;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class VisitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $visits = Visit::query();

        $visits_list = [];

        $hasSearch = $request->input('u') || $request->input('p');

        /** Search **/
        // Search By User
        if ($searchUser = $request->input('u')) {
            $queryUser = User::query();

            $queryUser->where('_id', '=', $searchUser);

            $users = $queryUser->get();

            $users_ids = [];

            foreach ($users as $user) {
                array_push($users_ids, $user->_id);
            }

            $user_visits = UserVisit::whereIn('fk_user_id', $users_ids)->get();

            foreach ($user_visits as $user_visit) {
                if (!in_array($user_visit->fk_visit_id, $visits_list, true)) {
                    array_push($visits_list, $user_visit->fk_visit_id);
                }
            }
        }

        // Search By Property
        if ($searchProperty = $request->input('p')) {
            $queryProperty = Property::query();

            $queryProperty->where('code', 'regexp', "/.*$searchProperty/i");

            $properties = $queryProperty->get();

            $property_ids = [];

            foreach ($properties as $property) {
                array_push($property_ids, $property->_id);
            }

            $property_visits = Visit::whereIn('fk_property_id', $property_ids)->get();

            foreach ($property_visits as $property_visit) {
                if (!in_array($property_visit->_id, $visits_list, true)) {
                    array_push($visits_list, $property_visit->_id);
                }
            }
        }

        if ($hasSearch) $visits->whereIn('_id', $visits_list);
        
        // Implements order by name
        $visits->orderBy('date', $request->input('sort', 'asc'));

        // Implements mongodb pagination
        $elementsPerPage = 25;
        $page = $request->input('page', 1);
        $total = $visits->count();

        $visits = $visits->offset(($page - 1) * $elementsPerPage)->limit($elementsPerPage)->get();

        foreach ($visits as $visit_key => $visit) {
            //Set Users
            $user_visits = UserVisit::query()->where('fk_visit_id', '=', $visit->_id)->get(["_id", "fk_visit_id", "fk_user_id"]);

            $users_ids = [];
            foreach ($user_visits as $user_visit) {
                array_push($users_ids, $user_visit->fk_user_id);
            }

            $usersResult = User::query()->whereIn('_id', $users_ids)->get(["_id", "name"]);
            $visits[$visit_key]->users = $usersResult;

            //Set property
            $visit->property = Property::where("_id", "=", $visit->fk_property_id)->get(["_id", "code"])->first();
        }

        $users = User::query()->get(['_id', 'name']);

        return [
            'visits' => $visits,
            'users' => $users,
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
        $visit = new Visit([
            'car' => $request->input("car"),
            'date' => $request->input("date"),
            'fk_property_id' => $request->input('fk_property_id'),
        ]);

        $users = $request->input('users');

        if (empty($users)) {
            return response()->json([
                'error' => "Nenhum servidor informado!",
            ], 400);
        }

        $property = Property::where('_id', '=', $request->input('fk_property_id'))->first();
    
        if (!$property) {
            return response()->json([
                'error' => "Esta propriedade não existe",
            ], 400);
        }

        // Save visits
        $visit->save();

        // Save user_visits
        foreach ($users as $key => $user) {
            $currentUser = User::where('_id', '=', $request->input('users')[$key])->first();
    
            if (!$currentUser) {
                return response()->json([
                    'error' => "Este servidor não existe",
                ], 400);
            }

            $user_visit = new UserVisit([
                'fk_visit_id' => $visit->_id,
                'fk_user_id' => $currentUser->_id,
            ]);

            $user_visit->save();
        }

        return response()->json([
            'visit' => $visit,
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
        $visit = Visit::find($id);

        //Set Users
        $users = [];
        $user_visits = UserVisit::query()->where('fk_visit_id', '=', $visit->_id)->get();

        foreach($user_visits as $key => $user_visit) {
            $users[$key] = User::query()->where('_id', '=', $user_visit->fk_user_id)->first();
        }

        $visit->users = $users;

        //Set property
        $visit->property = Property::where("_id", "=", $visit->fk_property_id)->first();

        return response()->json([
            'visit' => $visit,
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
        $visit = Visit::find($id);

        $users = $request->input('users');

        if (empty($users)) {
            return response()->json([
                'error' => "Nenhum servidor informado!",
            ], 400);
        }

        $property = Property::where('_id', '=', $request->input('fk_property_id'))->first();
    
        if (!$property) {
            return response()->json([
                'error' => "Esta propriedade não existe",
            ], 400);
        }

        // Save visits
        $visit->update($request->all());
        $visit->save();

        $user_visits = UserVisit::query()->where('fk_visit_id', '=', $visit->_id)->get();

        foreach($user_visits as $user_visit) {
            $deleted = new Garbage([
                'table' => 'user_visits',
                'deleted_id' => $user_visit->_id,
            ]);
            $deleted->save();

            UserVisit::find($user_visit->_id)->delete();
        }

        // Save user_visits
        foreach ($users as $key => $user) {
            $currentUser = User::where('_id', '=', $request->input('users')[$key])->first();
    
            if (!$currentUser) {
                return response()->json([
                    'error' => "Este servidor não existe",
                ], 400);
            }

            $user_visit = new UserVisit([
                'fk_visit_id' => $visit->_id,
                'fk_user_id' => $currentUser->_id,
            ]);

            $user_visit->save();
        }

        return response()->json([
            'visit' => $visit,
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
            'table' => 'visits',
            'deleted_id' => $id,
        ]);
        $deleted->save();

        Visit::find($id)->delete();

        $user_visits = UserVisit::query()->where('fk_visit_id', '=', $id)->get();

        foreach ($user_visits as $user_visit) {
            $deleted = new Garbage([
                'table' => 'user_visits',
                'deleted_id' => $user_visit->_id,
            ]);
            $deleted->save();

            UserVisit::find($user_visit->_id)->delete();
        }

        return response()->json([
            'deleted' => true,
        ], 204);
    }
}
