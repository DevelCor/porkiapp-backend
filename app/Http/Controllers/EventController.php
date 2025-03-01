<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $userFarmIds = $request->user()->farms()->pluck('farm_id');

        $events = Event::whereIn('farm_id', $userFarmIds)
                        ->with(['pig', 'treatment'])
                        ->get();

        return response()->json([
            'success' => true,
            'data'    => $events
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message'       => 'required|string|max:255',
            'pig_id'        => 'nullable',
            'farm_id'       => 'required',
            'reminder_date' => 'required|date',
            'type'          => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = $request->user();
        if (!$user->farms()->where('farm_id', $request->farm_id)->exists()) {
            return response()->json(['message' => 'You are not a member of this farm'], 403);
        }

        $event = Event::create($request->only([
            'message',
            'pig_id',
            'farm_id',
            'treatment_id',
            'reminder_date',
            'type'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully',
            'data'    => $event
        ], 201);
    }


    public function show(Request $request, $id)
    {
        $event = Event::with(['pig', 'treatment'])->find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        if (!$request->user()->farms()->where('farm_id', $event->farm_id)->exists()) {
            return response()->json(['message' => 'You are not a member of this farm'], 403);
        }

        return response()->json([
            'success' => true,
            'data'    => $event
        ], 200);
    }


    public function update(Request $request, $id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        // Verificar que el usuario pertenezca a la granja
        if (!$request->user()->farms()->where('farm_id', $event->farm_id)->exists()) {
            return response()->json(['message' => 'You are not a member of this farm'], 403);
        }

        // Validar los datos
        $validator = Validator::make($request->all(), [
            'message'       => 'nullable|string|max:255',
            'pig_id'        => 'nullable|exists:pigs,id',
            'treatment_id'  => 'nullable|exists:pig_treatments,id',
            'reminder_date' => 'nullable|date',
            'type'          => 'nullable|string|max:100',
            'active'        => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $event->update($request->only([
            'message',
            'pig_id',
            'treatment_id',
            'reminder_date',
            'type',
            'active'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully',
            'data'    => $event
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        // Verificar que el usuario pertenezca a la granja
        if (!$request->user()->farms()->where('farm_id', $event->farm_id)->exists()) {
            return response()->json(['message' => 'You are not a member of this farm'], 403);
        }

        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully'
        ], 200);
    }
}
