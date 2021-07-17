<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Requests\ProjectRequest;

class EditProjectController extends Controller
{
    public function stageTwoInfo(ProjectRequest $request, Project $project){

        $data = $request->validated();    
        unset($data['pictures']);
       
        $project->update($data);
        if($request->has('pictures')){
            $project->addMultipleMediaFromRequest(['pictures'])
            ->each(function ($fileAdder) {
                $fileAdder->toMediaCollection();
            });
        }
        
        return $this->successResponse($project->toArray(), 'Stage two of project updated successfully');
    }

    public function stageThreePublish(ProjectRequest $request, Project $project){
        $project->boosted = $request->boosted;
        $project->save();
        return $this->successResponse($project->toArray(), 'Project update completed');
    }
}
