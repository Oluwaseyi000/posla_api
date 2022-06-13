<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Helpers\AppConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Admin\SigninRequest;
use App\Http\Requests\Admin\SignupRequest;

class AdminAuthController extends Controller
{
    public function signup(SignupRequest $request){
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['pid'] = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
            $data['password'] = Hash::make($request->password);
            $admin = Admin::create($data);

            $admin['token'] = $admin->createToken('adminToken', ['role:'.AppConstant::ROLE_ADMIN])->plainTextToken;
            DB::commit();
            $this->getAuthUser('admin')->sendEmailVerificationNotification();
            return $this->successResponse($admin);
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
            return $this->errorResponse();
        }
    }

    public function login(SigninRequest $request){
        if (!auth('admin')->attempt($request->validated())) {
            return $this->validationFailResponse('Invalid Login Credentials');
        }

        $this->getAdminAuth()->token = $this->getAdminAuth()->createToken('adminToken', ['role:'.AppConstant::ROLE_ADMIN])->plainTextToken;
        return $this->successResponse($this->getAdminAuth());
    }

    public function logout(){
        $this->getAuthUser()->tokens()->delete();
        return $this->successResponse([], 'Logout Successful');
    }
}
