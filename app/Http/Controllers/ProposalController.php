<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Proposal;
use Illuminate\Http\Request;
use App\Http\Requests\ProposalRequest;

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

    public function projectProposals(Project $project){
        return $this->successResponse( $project->proposals);
    }

    public function acceptProposal(Proposal $proposal){
        if( $proposal->status == Proposal::ACCEPTED){
            return $this->errorResponse('Proposal already approved');
        }

        $proposal->status = Proposal::ACCEPTED;
        $proposal->save();
        return $this->successResponse();
    }

    public function shortlistProposal(Proposal $proposal){

        if( $proposal->status == Proposal::SHORTLISTED){
            return $this->errorResponse('Proposal already shortlisted');
        }elseif($proposal->status == Proposal::ACCEPTED){
            return $this->errorResponse('Proposal already approved');
        }

        $proposal->status = Proposal::SHORTLISTED;
        $proposal->save();

        return $this->successResponse(null, null, 'Proposal shortlisted');
    }

    public function shortlistedProposals(Project $project){
        $proposals =  $project->proposals->where('status', Proposal::SHORTLISTED);
        return $this->successResponse( $proposals);
    }
}
