<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FarmController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'location' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $invitationCode = strtoupper(Str::random(8));

        $farm = Farm::create([
            'name'            => $request->name,
            'location'        => $request->location,
            'invitation_code' => $invitationCode,
        ]);

        $user = $request->user();
        $user->assignFarmRole($farm, 'admin');

        return response()->json([
            'success' => true,
            'message'         => 'Farm created successfully',
            'data'            => [
                'farm'            => $farm,
                'invitation_code' => $invitationCode,
            ]
        ], 201);
    }

    public function join(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invitation_code' => 'required|string|size:8'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $invitationCode = strtoupper($request->invitation_code);
        $farm = Farm::where('invitation_code', $invitationCode)->first();

        if (!$farm) {
            return response()->json(['message' => 'Farm not found with provided invitation code'], 404);
        }

        $user = $request->user();

        if ($user->farms()->where('farm_id', $farm->id)->exists()) {
            return response()->json(['message' => 'You have already joined this farm'], 409);
        }

        $user->assignFarmRole($farm, 'member');

        return response()->json([
            'success' => true,
            'message' => 'Joined farm successfully',
            'data'    => [
                'farm' => $farm,
            ]
        ], 200);
    }
}
