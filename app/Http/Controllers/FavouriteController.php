<?php

namespace App\Http\Controllers;

use App\Http\Requests\FavoriteRequest;
use Illuminate\Http\Request;
use App\Models\DealFavourite;
use App\Models\ProjectFavourite;

class FavouriteController extends Controller
{
    public function addDeal(FavoriteRequest $request){
        $favourite = DealFavourite::where('deal_id', $request->deal_id)->where('user_id', $this->getAuthUser()->id)->first();
    
        if($favourite){
            $favourite->delete();
            $message = 'Deal removed from favorites';
            $data = [];
        }else{
            $this->getAuthUser()->dealFavourites()->create(['deal_id' => $request->deal_id]);
            $message = 'Deal added to favorites';
            $data = $this->getAuthUser()->dealFavourites->last();
    
        }
        return $this->successResponse($data, 'same endpoint for add add remove', $message);  
    }

    public function addProject(FavoriteRequest $request){
        $favourite = ProjectFavourite::where('project_id', $request->project_id)->where('user_id', $this->getAuthUser()->id)->first();
    
        if($favourite){
            $favourite->delete();
            $message = 'Deal removed from favorites';
            $data = [];
        }else{
            $this->getAuthUser()->projectFavourites()->create(['project_id' => $request->project_id]);
            $message = 'Project added to favorites';
            $data = $this->getAuthUser()->projectFavourites->last();
    
        }
        return $this->successResponse($data, 'same endpoint for add add remove', $message);  
    }
}
