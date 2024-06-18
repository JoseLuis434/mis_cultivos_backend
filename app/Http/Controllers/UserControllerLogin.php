<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserControllerLogin extends Controller
{
    //
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return response()->json(['message' => 'authorized']);
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->code_email = rand(100000, 999999);
        $user->save();
        return response()->json(['message' => 'created']);
    }

    public function sendCode(Request $request)
    {
        if($request->code == null){
            return response()->json(['message' => 'failed']);
        }
        $user = User::where('email', $request->email)->first();
        $code = $request->code;
        if ($user->code_email == $code) {
            $user->email_verified_at = now();
            $user->save();
            return response()->json(['message' => 'success']);
        } else {
            return response()->json(['message' => 'failed']);
        }
    }

    public function validateEmail(Request $request)
    {
        if (User::where('email', $request->email)->first()) {
            $user = User::where('email', $request->email)->first();
            $user->code_email = rand(100000, 999999);
            $user->save();
            return response()->json(['message' => 'success']);
        } else {
            return response()->json(['message' => 'error']);
        }
    }
    public function updatePassword(Request $request)
    {
        if ($request->code == NULL) {
            return response()->json(['message' => 'error']);
        }
        $user = User::where('email', $request->email)->where('code_email', $request->code)->first();
        if ($user) {
            $validator = Validator::make($request->all(), [
                'password' => [
                    'min:6',
                    'max:55',
                    function ($attribute, $value, $fail) use ($user) {
                        if (Hash::check($value, $user->password)) {
                            $fail('The new password cannot be the same as the current password.');
                        }
                    },
                ],
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()]);
            }
            $user->password = Hash::make($request->password);
            $user->code_email = NULL;
            $user->save();
            return response()->json(['message' => 'success']);
        } else {
            return response()->json(['message' => 'error']);
        }
    }

    public function getIdUser($email){
        $user = User::where("email", $email)->value('id');
        return $user;
    }
}
