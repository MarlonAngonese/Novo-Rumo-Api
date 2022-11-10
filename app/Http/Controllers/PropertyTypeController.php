<?php

namespace App\Http\Controllers;

use App\Models\Garbage;
use App\Models\PropertyType;
use Illuminate\Http\Request;

class PropertyTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = PropertyType::query();

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
            'property_types' => $result,
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
        $property_type = new PropertyType($request->input());

        $property_type->save();

        return response()->json([
            'property_type' => $property_type,
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
        $property_type = PropertyType::find($id);

        return response()->json([
            'property_type' => $property_type,
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
        $property_type = PropertyType::find($id);
        $property_type->update($request->all());

        $property_type->save();

        return response()->json([
            'property_type' => $property_type,
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
        $deleted = Garbage::new([
            'table' => 'property_types',
            'deleted_id' => $id,
        ]);
        $deleted->save();

        PropertyType::find($id)->delete();

        return response()->json([
            'deleted' => true,
        ], 204);
    }

    /**
     * Return all property types names
     * 
     * @return PropertyType $property_type
     */
    public function names() {
        $property_types = PropertyType::query()->get(["_id", "name"]);

        return response()->json([
            'property_types' => $property_types,
        ], 200);
    }
}
