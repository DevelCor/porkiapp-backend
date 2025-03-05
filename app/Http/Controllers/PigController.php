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
            'birth_date' => 'date'
        ]);

        // Generar un birth_code único
        do {
            $birthCode = $this->generateBirthCode();
        } while (Pig::where('birth_code', $birthCode)->exists());

        $user = $request->user();
        $pig = Pig::create([
            'gender'    => $request->gender,
            'age'       => $request->age,
            'weight'    => $request->weight,
            'parent_id' => $request->parent_id,
            'birth_code'=> $birthCode,
            'user_id'   => $user->id,
            'farm_id'   => $request->farm_id,
            'birth_date'   => $request->birth_date,
        ]);

        if (!$pig) {
            return response()->json(['message' => 'Error creating pig'], 500);
        }

        PigTreatment::applyStandardProtocol($pig);

        $data = [
            'success' => true,
            'message' => 'Pig created successfully',
            'data'    => $pig
        ];
        return response()->json($data, 201);
    }

    /**
     * Genera un código aleatorio de 10 caracteres (números y letras).
     *
     * @return string
     */
    private function generateBirthCode()
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';

        for ($i = 0; $i < 10; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $code;
    }

    //get all pigs or filter by farm_id
    public function index(Request $request)
    {
        $query = Pig::query();

        if ($request->has('farm_id')) {
            $query->where('farm_id', $request->farm_id);
        }

        $pigs = $query->get();

        if ($pigs->isEmpty()) {
            return response()->json(['message' => 'No pigs found'], 404);
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
