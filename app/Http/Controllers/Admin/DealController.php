<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StatusRequest;
use App\Models\Deal;
use Illuminate\Http\Request;

class DealController extends Controller
{
    public function listDeals(){
       $data =  Deal::paginate(request()->per_page ?? $this::PER_PAGE);
       return $this->successResponse($data);
    }

    public function viewDeal(Deal $deal){
       return $this->successResponse($deal);
    }

    public function updateStatus(StatusRequest $request, Deal $deal){
        $deal->status = $request->status;
       return $this->successResponse($deal);
    }
}
