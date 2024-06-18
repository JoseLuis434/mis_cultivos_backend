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
        $loginController = new UserControllerLogin;
        if ($this->validateLogin($request, $loginController)) {
            $id_user = $loginController->getIdUser($request->email);
            $crops = Crop::where('id_user', $id_user)->orderBy('created_at', 'desc')->get();
            return response()->json($crops);
        }
    }

    public function getCrop(Request $request)
    {
        $loginController = new UserControllerLogin;
        if ($this->validateLogin($request, $loginController)) {
            $id_device = $request->id_device;
            $crop = Crop::where('id_device', $id_device)->first();
            return response()->json($crop);
        }
    }
    public function addCrop(Request $request)
    {
        $loginController = new UserControllerLogin;
        if ($this->validateLogin($request, $loginController)) {
            if ($crop = Crop::where('id_device', $request->id_device)->first()) {
                if ($crop->created_at != null) {
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
                if ($request->unityAreaBase == "cm") {
                    $crop->container_area_base = ($request->areaBase) / 100;
                } else if ($request->unityAreaBase == "m") {
                    $crop->container_area_base = $request->areaBase;
                }

                if ($request->unityHeight == "cm") {
                    $crop->container_height = ($request->height) / 100;
                } else if ($request->unityHeight == "m") {
                    $crop->container_height = $request->height;
                }
                $id_user = $loginController->getIdUser($request->email);
                $crop->id_device = $request->id_device;
                $crop->name = $request->name;
                $crop->location = $request->location;
                $crop->type = $request->type;
                $crop->created_at = now();
                $crop->irrigation = $request->irrigation;
                $crop->id_user = $id_user;
                $crop->save();
                return response()->json(['message' => 'created']);
            } else {
                return response()->json(['message' => 'id_not_exists']);
            }
        } else {
            return response()->json(['message' => 'Unauthorized']);
        }
    }

    public function getMeasuresWaterContainer(Request $request)
    {
        $loginController = new UserControllerLogin;
        if ($this->validateLogin($request, $loginController)) {
            $id_user = $loginController->getIdUser($request->email);
            $measures = Crop::where('id_user', $id_user)->select('container_area_base', 'container_height')->get();
            return response()->json($measures);
        } else {
            return response()->json(['message' => 'Unauthorized']);
        }
    }

    public function setMeasuresWaterContainer(Request $request)
    {
        $loginController = new UserControllerLogin;
        if ($this->validateLogin($request, $loginController)) {
            $id_user = $loginController->getIdUser($request->email);
            $measures = Crop::where('id_user', $id_user)->first();
            if ($request->unityAreaBase == "cm") {
                $measures->container_area_base = ($request->areaBase) / 100;
            } else if ($request->unityAreaBase == "m") {
                $measures->container_area_base = $request->areaBase;
            }

            if ($request->unityHeight == "cm") {
                $measures->container_height = ($request->height) / 100;
            } else if ($request->unityHeight == "m") {
                $measures->container_height = $request->height;
            }
            $measures->save();
            return response()->json(['message' => true]);
        } else {
            return response()->json(['message' => false]);
        }
    }

    private function validateLogin(Request $request, UserControllerLogin $userControllerLogin)
    {
        $response = $userControllerLogin->login($request);
        $responseData = json_decode($response->getContent(), true);
        if ($responseData['message'] === 'authorized') {
            return true;
        }
        return false;
    }
}
