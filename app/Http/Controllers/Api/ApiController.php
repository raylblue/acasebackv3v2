<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class ApiController extends Controller
{
    //User register
    public function register(Request $request){
        echo "Register API";
        try {
        $validateUser = Validator::make($request->all(),
        [
            "name" => "required",
            "email"=> "required|email|unique:users,email",
            "password" => "required",

        ]);

        if($validateUser->fails()){
            return response()->json([
                'status'=> false,
                'message' => "validation error",
                'errors' => $validateUser->errors() 
            ],401 );
        }

        $user = User::create([
            "name" => $request->name,
            "email"=> $request->email,
            "password" => $request->password,
        ]);


         return response()->json([
                'status'=> true,
                'message' => "User created successfully",
                'token' => $user->createToken("API TOKEN")->plainTextToken 
            ],200 );
        
        }catch ( \Throwable $th){
            return response()->json([
                'status'=> false,
                'message' => $th->getMessage(),
            ],500 );
        }
    }

    //User login
    public function login(Request $request){
        try{

        $validateUser = Validator::make($request->all(),
        [
            "email"=> "required|email",
            "password" => "required",

        ]);

        if($validateUser->fails()){
            return response()->json([
                'status'=> false,
                'message' => "validation error",
                'errors' => $validateUser->errors() 
            ],401 );
        }

        if(!Auth::attempt($request->only (['email','password']))){
             return response()->json([
                'status'=> false,
                'message' => "Data does not match our records",
            ],401 );
        }

        $user = User::where("email", $request->email)->first();

        return response()->json([
                'status'=> true,
                'message' => "User logged in successfully",
                'token' => $user->createToken("API TOKEN")->plainTextToken 
            ],200 );
        

        }catch ( \Throwable $th){
            return response()->json([
                'status'=> false,
                'message' => $th->getMessage(),
            ],500 );
        }
    }

    //User data profile 
    public function profile(){
        $userData = auth()->user();
        return response()->json([
           'status'=> true,
            'message' => "Data of user profile",
            'data' => $userData,
            'id' => auth()->user()->id,
        ], 200);
    }

    //User logout 
    public function logout(){
        auth()->user()->tokens()->delete();
        return response()->json([
           'status'=> true,
            'message' => "User logged out",
            'data' => [],
        ], 200);
    }


}
