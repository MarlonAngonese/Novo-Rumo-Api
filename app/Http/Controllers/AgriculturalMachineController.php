<?php

namespace App\Http\Controllers;

use App\Models\AgriculturalMachine;
use App\Models\PropertyAgriculturalMachine;
use Illuminate\Http\Request;

class AgriculturalMachineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = AgriculturalMachine::query();

        // Implements search by name and email
        if ($search = $request->input('search')) {
            $query->where('name', 'regexp', "/.*$search/i");
        }

        // Implements order by name
        $query->orderBy('name', $request->input('sort', 'asc'));

        // Implements mongodb pagination
        $elementsPerPage = 25;
        $page = $request->input('page', 1);
        $total = $query->count();

        $result = $query->offset(($page - 1) * $elementsPerPage)->limit($elementsPerPage)->get();

        return [
            'agricultural_machines' => $result,
            'total' => $total,
            'page' => $page,
            'last_page' => ceil($total / $elementsPerPage),
            'search' => $search,
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
        $agricultural_machine = new AgriculturalMachine($request->input());

        $agricultural_machine->save();

        return response()->json([
            'agricultural_machine' => $agricultural_machine,
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
        $agricultural_machine = AgriculturalMachine::find($id);

        return response()->json([
            'agricultural_machine' => $agricultural_machine,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $agricultural_machine = AgriculturalMachine::find($id);
        $agricultural_machine->update($request->all());

        $agricultural_machine->save();

        return response()->json([
            'agricultural_machine' => $agricultural_machine,
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
        AgriculturalMachine::find($id)->delete();

        $property_agricultural_machines = PropertyAgriculturalMachine::query()->where('fk_agricultural_machine_id', '=', $id)->get();
        foreach($property_agricultural_machines as $property_agricultural_machine) {
            PropertyAgriculturalMachine::find($property_agricultural_machine->_id)->delete();
        }

        return response()->json([
            'deleted' => true,
        ], 204);
    }
}
