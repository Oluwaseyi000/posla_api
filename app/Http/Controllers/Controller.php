<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function successResponse(array $data, $message = 'successful', $status = true){
        return response([
            'status' => $status,
            'data' => $data,
            'message' => $message 
        ]);
    } 

    public function validationFailResponse($errors, $message = 'an error occurred', $status = false){
        return response([
            'status' => $status,
            'error' => $errors,
            'message' => $message 
        ], 401);
    }

    public function errorResponse($message = 'An error occurred', $status = false){
        return response([
            'status' => $status,
            'message' => $message 
        ]);
    }


    public function authUser(){
        return auth()->user();
    }
}
