<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Auth\SigninRequest;
use App\Http\Requests\Auth\SignupRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function signup(SignupRequest $request){
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['pid'] = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
            $data['password'] = Hash::make($request->password);
            $user = User::create($data);

            $user['token'] = $user->createToken('auth_token')->plainTextToken;
            // $user->sendEmailVerificationNotification();
            DB::commit();
            auth()->login($user);
            return $this->successResponse($user);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse();
        }
    }

    public function login(SigninRequest $request){
        if (!auth()->attempt($request->validated())) {
            return $this->validationFailResponse('Invalid Login Credentials');
        }
        $this->getAuthUser()->token = $this->getAuthUser()->createToken('authToken')->plainTextToken;
        return $this->successResponse($this->getAuthUser());
    }

    public function verifyEmail(User $user, Request $request) {
return 3;
        if (!$request->hasValidSignature()) {
            return $this->errorResponse('Invalid/Expired url provided');
        }

        if ($user->hasVerifiedEmail()) {
            return $this->errorResponse('Email Already Verified');
        }

        $user->markEmailAsVerified();
        return $this->successResponse([], 'Email verification successful');
    }

    public function resendVerificationEmail() {
        if ($this->getAuthUser()->hasVerifiedEmail()) {
            return $this->errorResponse('Email Already Verified');
        }

        $this->getAuthUser()->sendEmailVerificationNotification();
        return $this->successResponse([], 'Email verification link sent on your email');
    }

    public function forgotPassword(ForgotPasswordRequest $request) {
        Password::sendResetLink($request->validated());
        return $this->successResponse([], 'Reset password link sent on your email id');
    }

    public function resetPassword(ResetPasswordRequest $request) {
        $reset_password_status = Password::reset($request->validated(), function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        });

        if ($reset_password_status == Password::INVALID_TOKEN) {
            return $this->validationFailResponse( "Invalid token provided");
        }
        return $this->successResponse([], 'Password has been successfully changed');
    }

    public function changePassword(ChangePasswordRequest $request){
        if (!password_verify($request->old_password, $this->getAuthUser()->password)) {
            return $this->validationFailResponse([], 'Old password does not match');
        }
        $this->getAuthUser()->fill([
         'password' => Hash::make($request->new_password)
         ])->save();

        return $this->successResponse([], 'Password Changed');
    }

    public function logout(){
        $this->getAuthUser()->tokens()->delete();
        return $this->successResponse([], 'Logout Successful');
    }

    public function editProfile(Request $request){
        $user = $this->getAuthUser();
        $user->fill($request->all());
        $user->save();
        if($request->has('profile')){
            $user->addMediaFromRequest('profile')->toMediaCollection('profile');
        }
        return $this->successResponse($user);
    }
}

