<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Requests\ProjectRequest;
use Illuminate\Support\Facades\Notification;
use App\Models\CategoryNotificationSubscription;
use App\Notifications\GeneralNotification\NewProjectNotification;

class ProjectController extends Controller
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


        return $this->successResponse($project, 'Stage two (info) of project created successfully');
    }

    public function stageThreePublish(ProjectRequest $request){
        $project = Project::find($request->project_id);

        $project->boosted = $request->boosted;
        $project->action = 'completed';
        $project->save();

        // notify everyone;
        $categorySubscriptions = CategoryNotificationSubscription::whereIn('category_id' , [$project->category_id, $project->subcategory_id])->whereNotIn('user_id', [$this->getAuthUser()->id])->get('user_id');
        $notifiableUsers = User::find($categorySubscriptions);
        $message = (object) [];
        $message->from = $project->owner->name;
        $message->title = 'New ' .$project->subcategory->name. ' Project Created';
        $message->body = 'New ' .$project->subcategory->name. ' Project Created';
        $message->url = route('project.details', $project->id);
        Notification::send($notifiableUsers, new NewProjectNotification($message));

        return $this->successResponse($project, 'Project creation completed');
    }

    public function stageTwoInfoEdit(ProjectRequest $request, Project $project){

        $data = $request->validated();
        unset($data['pictures']);

        $project->update($data);
        if($request->has('pictures')){
            $project->addMultipleMediaFromRequest(['pictures'])
            ->each(function ($fileAdder) {
                $fileAdder->toMediaCollection();
            });
        }

        return $this->successResponse($project, 'Stage two of project updated successfully');
    }

    public function stageThreePublishEdit(ProjectRequest $request, Project $project){
        $project->boosted = $request->boosted;
        $project->save();
        return $this->successResponse($project, 'Project update completed');
    }
}
