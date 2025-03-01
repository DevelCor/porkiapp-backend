<?php

namespace App\Http\Controllers;

use App\Models\PigTreatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PigTreatmentController extends Controller
{
    public function getTreatmentsByPigId($pigId)
    {
        $treatments = PigTreatment::where('pig_id', $pigId)->get();

        if (!$treatments) {
            return response()->json(['message' => 'No treatments found for this pig'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $treatments
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pig_id'      => 'required|exists:pigs,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'dosage'      => 'nullable|string',
            'status'      => 'nullable|in:pending,administered'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $treatment = PigTreatment::create([
            'pig_id'      => $request->pig_id,
            'name'        => $request->name,
            'description' => $request->description,
            'dosage'      => $request->dosage,
            'status'      => $request->input('status', 'pending')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Treatment created successfully',
            'data'    => $treatment
        ], 201);
    }


    public function update(Request $request, $id)
    {
        $treatment = PigTreatment::find($id);

        if (!$treatment) {
            return response()->json(['message' => 'Treatment not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'        => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'dosage'      => 'nullable|string',
            'status'      => 'nullable|in:pending,administered'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $treatment->update($request->only([
            'name',
            'description',
            'dosage',
            'status'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Treatment updated successfully',
            'data'    => $treatment
        ], 200);
    }
}
