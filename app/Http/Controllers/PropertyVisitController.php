<?php

namespace App\Http\Controllers;

use App\Models\PropertyVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PropertyVisitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $property_visits = PropertyVisit::query();
        return $property_visits->get();

        

        // $query = User::query();

        // // Implements search by name and email
        // if ($search = $request->input('search')) {
        //     $query->where('name', 'regexp', "/.*$search/i")
        //         ->orWhere('email', 'regexp', "/.*$search/i");
        // }

        // // Implements order by name
        // $query->orderBy('name', $request->input('sort', 'asc'));

        // // Implements mongodb pagination
        // $elementsPerPage = 25;
        // $page = $request->input('page', 1);
        // $total = $query->count();

        // $result = $query->offset(($page - 1) * $elementsPerPage)->limit($elementsPerPage)->get();

        // return [
        //     'users' => $result,
        //     'total' => $total,
        //     'page' => $page,
        //     'last_page' => ceil($total / $elementsPerPage),
        //     'search' => $search,
        // ];
    }

    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|unique:users|max:255|email:rfc,dns',
    //         'name' => 'required|max:255',
    //         'password' => 'required|min:8|max:255'
    //     ], [
    //         'email.email' => 'Este formato de E-mail é inválido!',
    //         'email.unique' => 'Este E-mail já está em uso!',
    //         'email.required' => 'O campo E-mail é requerido!',
    //         'email.size' => 'O campo E-mail precisa ter menos de :max caracteres!',
    //         'name.required' => 'O campo Nome é requerido!',
    //         'name.size' => 'O campo Nome precisa ter menos de :max caracteres!',
    //         'password.required' => 'O campo Senha é requerido!',
    //         'password.size' => 'O campo Senha precisa ter entre :min e :max caracteres!',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'error' => $validator->errors()->first(),
    //         ], 400);
    //     }

    //     $user = new User($request->input());

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
