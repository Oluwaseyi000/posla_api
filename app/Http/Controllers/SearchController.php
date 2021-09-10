<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function all(){
        try {
            $query = request()->q;
            $data = (object)[];
            $data->deal = Deal::search($query)->where('status', Deal::ENABLED)->get();
            $data->project = Project::search($query)->where('status', 1)->get();
            $data->user = User::search($query)->get();
            return $this->successResponse($data);
        } catch (\Throwable $th) {
        }
    }

    public function deals(){
        try {
            $query = request()->q;
            $data = Deal::search($query)->where('status', 1)->paginate(10);
            return $this->successResponse($data);
        } catch (\Throwable $th) {
        }
    }

    public function projects(){
        try {
            $query = request()->q;
            $data = Project::search($query)->where('status', 1)->paginate(5);
            return $this->successResponse($data);
        } catch (\Throwable $th) {
         }
    }

    public function users(){
        try {
            $query = request()->q;
            $data = User::search($query)->paginate(10);
            return $this->successResponse($data);

        } catch (\Throwable $th) {

        }
    }
}
