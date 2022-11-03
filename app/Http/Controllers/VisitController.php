<?php

namespace App\Http\Controllers;

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
        $users = [];

        // Implements order by name
        $visits->orderBy('date', $request->input('sort', 'asc'));

        // Implements mongodb pagination
        $elementsPerPage = 25;
        $page = $request->input('page', 1);
        $total = $visits->count();

        $visits = $visits->offset(($page - 1) * $elementsPerPage)->limit($elementsPerPage)->get();

        foreach ($visits as $visit_key => $visit) {
            $user_visits = UserVisit::query()->where('fk_visit_id', '=', $visit->_id)->get();

            foreach($user_visits as $key => $user_visit) {
                $users[$key] = User::query()->where('_id', '=', $user_visit->fk_user_id)->first();
            }

            $visits[$visit_key]->users = $users;
            $users = [];
        }

        return [
            'visits' => $visits,
            'total' => $total,
            'page' => $page,
            'last_page' => ceil($total / $elementsPerPage),
            // 'search' => $search,
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

        // Save property_visits
        $property_visit = new PropertyVisit([
            'fk_property_id' => $property->_id,
            'fk_visit_id' => $visit->_id
        ]);

        $property_visit->save();

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

    // /**
    //  * Display the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function show($id)
    // {
    //     $user = User::find($id);

    //     return response()->json([
    //         'user' => $user,
    //     ], 200);
    // }

    // /**
    //  * Update the specified resource in storage.
    //  *
    //  * @param  int  $id
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function update($id, Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|max:255|email:rfc,dns',
    //         'name' => 'required|max:255',
    //         'password' => 'required|min:8|max:255'
    //     ], [
    //         'email.email' => 'Este formato de E-mail é inválido!',
    //         'email.required' => 'O campo E-mail é requerido!',
    //         'email.size' => 'O campo E-mail precisa ter menos de :max caracteres!',
    //         'name.required' => 'O campo Nome é requerido!',
    //         'name.size' => 'O campo Nome precisa ter menos de :max caracteres!',
    //         'password.required' => 'O campo Senha é requerido!',
    //         'password.size' => 'O campo Senha precisa ter entre :min e :max caracteres!',
    //     ]);

    //     $user = User::where('email', $request->input('email'))->first();

    //     if ($user->_id != $id) {
    //         return response()->json([
    //             'error' => 'Este E-mail já está em uso!',
    //         ], 400);
    //     }

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'error' => $validator->errors()->first(),
    //         ], 400);
    //     }

    //     $user = User::find($id);
    //     $user->update($request->all());

    //     // Encode the password
    //     $user->password = Hash::make(
    //         $request->input('password'),
    //         [
    //             'rounds' => 10,
    //             'salt' => env('SALT'),
    //         ],
    //     );

    //     $user->save();

    //     return response()->json([
    //         'user' => $user,
    //     ], 201);
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function destroy($id)
    // {
    //     User::find($id)->delete();

    //     return response()->json([
    //         'deleted' => true,
    //     ], 204);
    // }
}
