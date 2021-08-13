<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProposalRequest;
use App\Models\Proposal;
use Illuminate\Http\Request;

class ProposalController extends Controller
{
    public function bid(ProposalRequest $request){
        $data = array_merge($request->validated(), ['status' => Proposal::SUBMITTED]);
        $this->getAuthUser()->proposals()->create($data);
        return $this->successResponse($this->getAuthUser()->proposals->last());
    }

    public function withdraw(ProposalRequest $request){
        Proposal::where('id', $request->proposal_id)->delete();
        return $this->successResponse([],'','Proposal Deleted');
    }
}
