<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VerificationController extends Controller
{
    public function verify($user_id, Request $request) {
        if (!$request->hasValidSignature()) {
            return response()->json(["msg" => "Invalid/Expired url provided."], 401);
        }
    
        $user = User::findOrFail($user_id);
    
        if ($user->hasVerifiedEmail()) {
            return response()->json(["msg" => "Email already verified"]);
        }
        
        $user->markEmailAsVerified();
        return response()->json(["msg" => "Email verification successful"]);
    }
    
    public function resend() {
        $user_id="93dfe692-56ba-4034-935a-dd6dcf7d6004";
       $user = User::findOrFail($user_id);
        if ($user->hasVerifiedEmail()) {
            return response()->json(["msg" => "Email already verified."], 400);
        }
    
        $user->sendEmailVerificationNotification();
    
        return response()->json(["msg" => "Email verification link sent on your email id"]);
    }
}
