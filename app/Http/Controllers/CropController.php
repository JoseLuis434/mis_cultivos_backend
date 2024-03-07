<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\UserControllerLogin;
use App\Models\Crop;
use Illuminate\Support\Facades\Validator;

class CropController extends Controller
{
    public function getCrops(Request $request)
    {
        $crops = Crop::whereNotNull('crop_verified_at')->orderBy('crop_verified_at', 'desc')->get();
        return response()->json($crops);
    }
    public function addCrop(Request $request)
    {
        $loginController = new UserControllerLogin();
        $response = $loginController->login($request);
        $responseData = json_decode($response->getContent(), true);
        if ($responseData['message'] === 'authorized') {
            if ($crop = Crop::where('id_device', $request->id_device)->first()) {
                if ($crop->crop_verified_at != null) {
                    return response()->json(['message' => 'already_exists']);
                }
                $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:255',
                    'location' => 'required|string|max:255',
                    'type' => 'required|string|max:75',
                    'irrigation' => 'required|string|max:75',
                ]);
                if ($validator->fails()) {
                    return response()->json(['message' => $validator->errors()]);
                }
                $crop->id_device = $request->id_device;
                $crop->name = $request->name;
                $crop->location = $request->location;
                $crop->type = $request->type;
                $crop->crop_verified_at = now();
                $crop->irrigation = $request->irrigation;
                $crop->save();
                return response()->json(['message' => 'created']);
            } else {
                return response()->json(['message' => 'id_not_exists']);
            }
        } else {
            return response()->json(['message' => 'Unauthorized']);
        }
    }
}
