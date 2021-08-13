<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use Illuminate\Http\Request;
use App\Http\Requests\DealRequest;

class DealController extends Controller
{
    public function stageTwoInfo(DealRequest $request){
        $data = $request->validated();    
        unset($data['pictures']);
        $data['action'] = 'creating';
        $data['user_id'] = $this->getAuthUser()->id;
       

        $deal = Deal::create($data);
        $deal->addMultipleMediaFromRequest(['pictures'])
            ->each(function ($fileAdder) {
                $fileAdder->toMediaCollection();
            });
        return $this->successResponse($deal, 'Stage two (info) of deal created successfully');
    }

    public function stageThreePrice(DealRequest $request, Deal $deal){
        $deal->types()->createMany($request->types);
        return $this->successResponse($deal->types, 'Stage three (price) of project created successfully');
    }

    public function stageFourRequirement(DealRequest $request, Deal $deal){
        $deal->requirements()->createMany($request->questions);
        return $this->successResponse($deal->requirements, 'Stage four (requirements) of project created successfully');
    }

    public function stageFivePublish(DealRequest $request, Deal $deal){
        $deal->action = 'completed';
        $deal->save();
        return $this->successResponse($deal, 'Deal creation completed');
    }

    public function stageTwoInfoEdit(DealRequest $request, Deal $deal){
        $data = $request->validated();    
        unset($data['pictures']);
       
        $deal->update($data);
        if($request->has('pictures')){
            $deal->addMultipleMediaFromRequest(['pictures'])
            ->each(function ($fileAdder) {
                $fileAdder->toMediaCollection();
            });
        }
        return $this->successResponse($deal, 'Stage two (info) of deal updated successfully');
    }

    public function stageThreePriceEdit(DealRequest $request, Deal $deal){
        // return $deal->types()->getRelatedIds();
        return $deal->types;
        $deal->types()->createMany($request->types);
        return $this->successResponse($deal->types, 'Stage three (price) of project created successfully');
    }

    public function getDealTypes(Deal $deal){
        return $deal->types;
    }
    public function getDealRequirements(Deal $deal){
        return $deal->requirements;
    }
}
