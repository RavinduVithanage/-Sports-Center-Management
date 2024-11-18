<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Password;

class AuthController extends Controller
{
    public function userRegister(Request $request):JsonResponse{
        
        $validate=$request->validate([
            "name"=>'required|string|max:255',
            "email"=>'required|email|unique:users,email|max:255',
            "password"=>'required|string|min:8'

        ]);

        $user=User::create([

            "name"=>$validate['name'],
            "email"=>$validate['email'],
            "password"=>Hash::make($validate['password'])


        ]);

        if($user){
           $token=$user->createToken('token-name')->plainTextToken;

           return response()->json([
                "message"=>"Registration successful",
                "token_type"=>"bearer",
                "token"=>$token


           ]);
        }else{

            return response()->json([
                "message"=>"registation not sucessfull somthing error",
           ]);

        }
    }

    public function userLogin(Request $request):jsonResponse{

        $validate=$request->validate([
            "email"=>'required|email|max:255',
            "password"=>'required|string|min:8'

        ]);

        $user=User::where('email',$request->email)->first();

        if(!$user || !Hash::check($request->password,$user->password)){

            return response()->json([
                "message"=>"invalid credintial",
           ]);
        }else{

            $token=$user->createToken('token-name')->plainTextToken;

            return response()->json([
                 "message"=>"registation sucessfull",
                 "token_type"=>"bearer",
                 "token"=>$token
 
 
            ]);
        }
    }
    public function userLogout(Request $request):JsonResponse{

        if($request->user()){
            $request->user->tokens()->delete();
            return response()->json([
                "message"=>"Logout sucessfull",
           ]);
        }
        return response()->json([
            "message"=>"user not login",
       ]);
    }

    public function passwordReset(Request $request):JsonResponse{

        $request->validate(['email' => 'required|email']);

        $user=User::where('email',$request->email)->first();

        if($user){
            $status = Password::sendResetLink(
                $request->only('email')
                
            );

            return response()->json([
                "message"=>"now you can reset password",
                "status"=>$status
           ]);
        }else{
            return response()->json([
                "message"=>"your not register",
           ]);
        }
    }
}
