<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Requests\ProjectRequest;

class CreateProjectController extends Controller
{
    public function stageTwoInfo(ProjectRequest $request){

        $data = $request->validated();    
        unset($data['pictures']);
        $data['action'] = 'creating';
        $data['user_id'] = $this->getAuthUser()->id;
       
        $project = Project::create($data);
 
        $project->addMultipleMediaFromRequest(['pictures'])
        ->each(function ($fileAdder) {
            $fileAdder->toMediaCollection();
        });
        
        return $this->successResponse($project->toArray(), 'Stage two of project created successfully');
    }

    public function stageThreePublish(ProjectRequest $request){
        $project = Project::find($request->project_id);
        $project->boosted = $request->boosted;
        $project->action = 'completed';
        $project->save();

        return $this->successResponse($project->toArray(), 'Project creation completed');
    }
}
