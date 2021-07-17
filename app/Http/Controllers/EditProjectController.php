<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Requests\Projects\CreateProjectRequest;

class EditProjectController extends Controller
{
    public function stageTwoInfo(CreateProjectRequest $request, Project $project){

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

    public function stageThreePublish(CreateProjectRequest $request, Project $project){
        $project->boosted = $request->boosted;
        $project->save();

        return $this->successResponse($project->toArray(), 'Project creation completed');
    }
}
