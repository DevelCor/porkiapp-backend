<?php

namespace App\Http\Controllers;

use App\Models\Pig;
use App\Models\PigTreatment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PigController extends Controller
{
    //create a new pig
    public function store(Request $request)
    {
        $request->validate([
            'gender'    => 'required|string',
            'weight'    => 'required|numeric',
            'parent_id' => 'integer|exists:pigs,id',
            'farm_id'   => 'required|exists:farms,id',
            'birth_code'=> 'string',
            'birth_date' => 'date'
        ]);

        $user = $request->user();
        $pig = Pig::create([
            'gender'    => $request->gender,
            'age'       => $request->age,
            'weight'    => $request->weight,
            'parent_id' => $request->parent_id,
            'birth_code'=> $request->birth_code,
            'user_id'   => $user->id,
            'farm_id'   => $request->farm_id,
            'birth_date'   => $request->birth_date,
        ]);

        if (!$pig) {
            return response()->json(['message' => 'Error creating pig'], 500);
        }

        // Aplicar el protocolo estándar (maneja recién nacidos y padrotes).
        PigTreatment::applyStandardProtocol($pig);

        $data = [
            'success' => true,
            'message' => 'Pig created successfully',
            'data'    => $pig
        ];
        return response()->json($data, 201);
    }
    //get all pigs
    public function index()
    {
        $pigs = Pig::all();

        if (!$pigs) {
            return response()->json(['message' => 'Pigs not found'], 404);
        }

        $data = [
            'success' => true,
            'message' => 'Pigs retrieved successfully',
            'data' => $pigs
        ];

        return response()->json($data, 200);
    }

    //get pig by id
    public function show($id)
    {
        $pig = Pig::with('treatments')->find($id);

        if (!$pig) {
            return response()->json(['message' => 'Pig not found'], 404);
        }

        $data = [
            'success' => true,
            'message' => 'Pig retrieved successfully',
            'data' => $pig
        ];

        return response()->json($data, 200);
    }

    //update pig
    public function update(Request $request, $id)
    {
        $request->validate([
            'gender'    => 'string',
            'age'       => 'integer',
            'weight'    => 'numeric',
            'parent_id' => 'integer|exists:pigs,id',
            'farm_id'   => 'exists:farms,id',
            // Si quieres permitir 'postpartum' como booleano, puedes validarlo aquí:
            'postpartum'=> 'boolean'
        ]);

        $pig = Pig::find($id);

        if (!$pig) {
            return response()->json(['message' => 'Pig not found'], 404);
        }

        $pig->update($request->all());
        $pig->refresh();

        // Si es hembra y recibe la bandera postpartum = true, aplicar protocolo postparto
        if ($request->has('postpartum') && $request->postpartum === true && $pig->gender === 'female') {
            PigTreatment::applyPostpartumProtocol($pig);
        }

        $data = [
            'success' => true,
            'message' => 'Pig updated successfully',
            'data'    => $pig
        ];

        return response()->json($data, 200);
    }

    //delete pig
    public function destroy($id)
    {
        $pig = Pig::find($id);

        if (!$pig) {
            return response()->json(['message' => 'Pig not found'], 404);
        }

        $pig->delete();

        $data = [
            'success' => true,
            'message' => 'Pig deleted successfully',
        ];

        return response()->json($data, 200);
    }
}
