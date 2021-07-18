<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function myDeals(){
        $myDeals =  $this->getAuthUser()->deals->toArray();
        return $this->successResponse($myDeals);
    }
   
    public function myProjects(){
        $myProjects =  $this->getAuthUser()->projects->toArray();
        return $this->successResponse($myProjects);
    }

    public function myProfile(){
        $myProfile =  $this->getAuthUser();
        return $this->successResponse($myProfile->toArray());
    }

    public function myOrders(){
        $myOrders =  $this->getAuthUser();
        return $this->successResponse($myOrders->toArray());
    }

    public function myProjectBids(){
        $myOrders =  $this->getAuthUser();
        return $this->successResponse($myOrders->toArray());
    }

    public function dashboard(){
        $data = User::from('users as user' )
            ->where('user.id', $this->getAuthUser()->id)
            ->with('activeDeals', 'activeProjects')->get();

        return $this->successResponse($data->toArray());  
    }

    public function vacation(){
        $user = $this->getAuthUser();
        if($user->status == User::ACTIVE){
            $user->status = User::VACATION;
            $message = 'Vacation mode activated, enjoy your vacation';

        }elseif($user->status == User::VACATION){
            $user->status = User::ACTIVE;
            $message = 'Vacation mode deactivated';
        }

        $user->save();

        return $this->successResponse(['status' => $user->status], 'same endpoint for vacation and off-vacation', $message);  
    }

   

//     public function categoryDeals(Category $category){
//         $category = Category::from('categories as category' )
//             ->where('category.id', $category->id)
//             ->with('deals', 'children')->get();

//         return $this->successResponse($category->toArray());  
//     }
}
