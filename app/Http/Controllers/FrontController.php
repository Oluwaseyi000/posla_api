<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Project;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FrontController extends Controller
{
    public function allProjects(){
        $projects = Project::from('projects as project' )
            ->leftJoin('categories as category', 'category.id', '=', 'project.category_id')
            ->select(['project.id', 'title', 'budget', 'project.description', 'tags', 'boosted', 'category.id as category_id', 'category.name as category_name', 'project.created_at'])
            ->get()
            ->groupBy('category.name');
        return $this->successResponse($projects->toArray(), 'Projects are grouped by category');
    }

    public function singleProject(Project $project){
        $project = Project::from('projects as project' )
            ->where('project.id', $project->id)
            ->leftJoin('categories as category', 'category.id', '=', 'project.category_id')
            ->leftJoin('categories as subcategory', 'subcategory.id', '=', 'project.subcategory_id')
            ->leftJoin('users as user', 'user.id', '=', 'project.user_id')
            
            ->select([
                // 'media.*',
                'project.id', 'title', 'budget', 'project.description', 'tags', 'boosted', 'project.created_at',
                'category.id as category_id', 'category.name as category_name',
                'subcategory.id as subcategory_id', 'subcategory.name as subcategory_name',
                'user.name as user_name', 'user.id as user_id', 'user.created_at as member_since', 'user.gender as user_gender', 
                // 'user.description as user_description' 
                // ,
                    DB::raw('(select count(*) FROM proposals where proposals.project_id = project.id) as proposal_count'),
                ])
            ->get();
            

        return $this->successResponse($project->toArray());
    }

    public function featuredProjects(){
        $feature_projects = Project::where(['boosted' =>true, 'status'=> true])->inRandomOrder()->limit(3)->get();
        return $this->successResponse($feature_projects->toArray(), 'Featured project limit to 3 ');
    }

    public function allDeals(){
        $deals = Deal::from('deals as deal')
            ->leftJoin('users as user', 'user.id', '=', 'deal.user_id')
            ->leftJoin('categories as category', 'category.id', '=', 'deal.category_id')
            ->select(['deal.id', 'deal.description', 'tags', 'boosted', 'category.id as category_id', 'category.name as category_name', 'user.name as user_name', 'deal.created_at'])
            ->get()
            ->groupBy('category.name');
        return $this->successResponse($deals->toArray(), 'Deals are grouped by category');
    }

    public function singleDeal(Deal $deal){
        $data = Deal::from('deals as deal' )
            ->with(['types'])
            ->where('deal.id', $deal->id)
            ->leftJoin('categories as category', 'category.id', '=', 'deal.category_id')
            ->leftJoin('categories as subcategory', 'subcategory.id', '=', 'deal.subcategory_id')
            ->leftJoin('users as user', 'user.id', '=', 'deal.user_id')
            ->select([
                'deal.id', 'title', 'deal.description', 'tags', 'boosted', 'deal.created_at',
                'category.id as category_id', 'category.name as category_name',
                'subcategory.id as subcategory_id', 'subcategory.name as subcategory_name',
                'user.name as user_name', 'user.id as user_id', 'user.created_at as member_since', 'user.gender as user_gender', 
                ])
            ->get();
            $data['seller_other_deals'] =  $deal->sellerOtherDeals();
            $data['category_other_deals'] =  $deal->categoryOtherDeals();

        return $this->successResponse($data->toArray());
    }

    public function categoryProjects(Category $category){
        $category = Category::from('categories as category' )
            ->where('category.id', $category->id)
            ->with('projects', 'children')->get();

        return $this->successResponse($category->toArray());  
    
    }
    public function categoryDeals(Category $category){
        $category = Category::from('categories as category' )
            ->where('category.id', $category->id)
            ->with('deals', 'children')->get();

        return $this->successResponse($category->toArray());  
    }
    

    public function featuredDeals(){
        // $feature_projects = Project::where(['boosted' =>true, 'status'=> true])->inRandomOrder()->limit(2)->get();
        $categories_deals = Deal::where(['status'=> true])->get()->groupBy('category_id', true);
        // $latest_projects =  Project::where(['status'=> true])->inRandomOrder()->limit(2)->get();

        return $this->successResponse($categories_deals->toArray(), 'Deals are grouped by category');
    }
}
