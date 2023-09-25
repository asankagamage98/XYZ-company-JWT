<?php

namespace App\Http\Controllers;
use Auth;
use Validator;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller

{
  //creare constructor

    public function _construct(){
        $this->middleware('auth:api',['expect' => ['login','register']]);
    }

    //create new user
    public function register (Request $resquest){
        $validator = Validator::make($resquest ->all(),[
            'name' =>'required',
            'email'=>'required|String|email|unique:users',
            'password' =>'required|String|confirmed|min:6'
        ]);

        if($validator->fails()){
             return response()->json($validator->errors()->toJson(),400);
        }
        $user = User::create(array_merge(
             $validator->validated(),
              ['password' =>bcrypt($resquest->password)]
        
        ));
        

        return response()->json([
            'message'=>'User successfully registerd',
            'user' =>$user
        ],201);

        }

    //user login
    public function login (Request $resquest){
        $validator = Validator::make($resquest ->all(),[
            'email'=>'required|email',
            'password' =>'required|String|min:6'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),422);
         }
        if(!$token =auth()->attempt($validator->validated())){
            return response()->json(['error' => 'Unauthorized'],401);
        }
        return $this->createNewToken($token);
    }

    //create new token
    public function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
    

  
    
    
}

 

