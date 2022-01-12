<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const PER_PAGE =  10;

    public function successResponse($data = null, $comment = null, $message = 'successful', $status = true){
        $response = [
            'status' => $status,
            'message' => $message
        ];
        $response = $data ? array_merge($response, ['data' => $data]) : $response;
        $response = $comment ? array_merge($response, ['comment' => $comment]) : $response;
        return response()->json($response);

    }

    public function validationFailResponse($errors, $message = 'an error occurred', $status = false){
        return response()->json([
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


    public function getAuthUser($guard = null){
        return auth($guard)->user();
    }

    public function getAdminAuth(){
        return auth('admin')->user();
    }
}
