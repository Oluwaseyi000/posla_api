<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\User;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function myDeals(Request $request){
        $myDeals =  $this->getAuthUser()->deals;
        return $this->successResponse($myDeals);
    }

    public function myProjects(){
        $myProjects =  $this->getAuthUser()->projects;
        return $this->successResponse($myProjects);
    }

    public function myProfile(){
        $myProfile =  $this->getAuthUser();
        return $this->successResponse($myProfile);
    }

    public function myOrders(){
        $myOrders =  $this->getAuthUser()->orders;
        return $this->successResponse($myOrders);
    }

    public function myOrdersOwner(){
        $myOrders =  $this->getAuthUser()->ordersOwner;
        return $this->successResponse($myOrders);
    }

    public function myFavourites(){
        $myFavourites =  User::where('id', $this->getAuthUser()->id)->with(['dealFavourites', 'projectFavourites'])->select(['id'])->get();
        return $this->successResponse($myFavourites);
    }

    public function myProjectBids(){
        $myProposals =  $this->getAuthUser()->proposals;
        return $this->successResponse($myProposals);
    }

    public function dashboard(){
        $data = User::from('users as user' )
            ->where('user.id', $this->getAuthUser()->id)
            ->with('activeDeals', 'activeProjects')->get();

        return $this->successResponse($data);
    }

    public function vacation(){
        $user = $this->getAuthUser();

        if($user->status == User::ACTIVE){
            $user->status = User::VACATION;
            $message = 'Vacation mode activated, enjoy your vacation';
            $user->save();
        }elseif($user->status == User::VACATION){
            $user->status = User::ACTIVE;
            $message = 'Vacation mode deactivated';
             $user->save();
        }elseif($user->status == User::DISABLE){
            $message = 'Cant set account to vacation, Your account is disabled';
        }

        return $this->successResponse(['status' => $user->status], 'same endpoint for vacation and off-vacation', $message);
    }

    public function earningsWithdrawal(){
        $transactions = (object)[];
        $transactions->history =  collect($this->getAuthUser()->earnings, $this->getAuthUser()->payments);
        $transactions->total_earnings =  $this->getAuthUser()->totalEarnings();
        $transactions->total_withdrawn =  $this->getAuthUser()->totalEarnings();
        $transactions->available_for_withdraw =  $this->getAuthUser()->totalEarnings();
        $transactions->pending_clearance=  $this->getAuthUser()->totalEarnings();

        return $this->successResponse($transactions);
    }
}
