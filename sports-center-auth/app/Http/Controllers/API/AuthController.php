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
                "message"=>"registation sucessfull",
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
                 "message"=>"login sucessfull",
                 "token_type"=>"bearer",
                 "token"=>$token
 
 
            ]);
        }
    }
    public function userLogout(Request $request):JsonResponse{

        if($request->user()){
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                "message"=>"Logout sucessfull",
           ]);
        }else{
        return response()->json([
            "message"=>"user not login",
       ]);
         }
    }

    public function passwordReset(Request $request): JsonResponse
    {
        // Validate the email input
        $request->validate(['email' => 'required|email']);
    
        // Check if the user exists
        $user = User::where('email', $request->email)->first();
    
        if (!$user) {
            return response()->json([
                "message" => "You are not registered.",
            ], 404);
        }
    
        // Attempt to send the reset link
        $status = Password::sendResetLink(
            $request->only('email')
        );
    
        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                "message" => "Password reset link sent successfully.",
                "status" => __($status),
            ], 200);
        } else {
            return response()->json([
                "message" => "Failed to send password reset link. Please try again later.",
                "status" => __($status),
            ], 500);
        }
    }
}
