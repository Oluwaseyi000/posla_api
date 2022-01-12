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
       $projects = DB::table('projects')
            ->leftJoin('categories as category', 'category.id', '=', 'projects.category_id')
            ->select([ 'projects.id', 'title', 'budget', 'projects.description', 'tags', 'boosted', 'category.id as category_id', 'category.name as category_name', 'projects.created_at'])
            ->paginate(request()->per_page ?? $this::PER_PAGE);
        return $this->successResponse($projects, 'Projects are grouped by category');
    }

    public function singleProject(Project $project){
        $project = Project::
            where('projects.id', $project->id)->
            leftJoin('categories as category', 'category.id', '=', 'projects.category_id')
            ->leftJoin('categories as subcategory', 'subcategory.id', '=', 'projects.subcategory_id')
            ->leftJoin('users as user', 'user.id', '=', 'projects.user_id')

            ->select([
                // 'media.*',
                'projects.id', 'title', 'budget', 'projects.description', 'tags', 'boosted', 'projects.status', 'projects.created_at',
                'category.id as category_id', 'category.name as category_name',
                'subcategory.id as subcategory_id', 'subcategory.name as subcategory_name',
                'user.name as user_name', 'user.id as user_id', 'user.created_at as member_since', 'user.gender as user_gender',
                // 'user.description as user_description'
                // ,
                    DB::raw('(select count(*) FROM proposals where proposals.project_id = projects.id) as proposal_count'),
                ])
            ->get();


        return $this->successResponse($project);
    }

    public function featuredProjects(){
        $feature_projects = Project::where(['boosted' =>true, 'status'=> true])->inRandomOrder()->limit(3)->get();
        return $this->successResponse($feature_projects, 'Featured project limit to 3 ');
    }

    public function allDeals(){
        $deals = DB::table('deals')->
            leftJoin('users as user', 'user.id', '=', 'deals.user_id')
            ->leftJoin('categories as category', 'category.id', '=', 'deals.category_id')
            ->select(['deals.id', 'deals.description', 'tags', 'boosted', 'category.id as category_id', 'category.name as category_name', 'user.name as user_name', 'deals.created_at'])
            ->paginate(request()->per_page ?? $this::PER_PAGE);
        return $this->successResponse($deals, 'Deals are grouped by category');
    }

    public function singleDeal(Deal $deal){
        $data = Deal::with(['types'])
            ->where('deals.id', $deal->id)
            ->leftJoin('categories as category', 'category.id', '=', 'deals.category_id')
            ->leftJoin('categories as subcategory', 'subcategory.id', '=', 'deals.subcategory_id')
            ->leftJoin('users as user', 'user.id', '=', 'deals.user_id')
            ->select([
                'deals.id', 'title', 'deals.description', 'tags', 'boosted', 'deals.created_at',
                'category.id as category_id', 'category.name as category_name',
                'subcategory.id as subcategory_id', 'subcategory.name as subcategory_name',
                'user.name as user_name', 'user.id as user_id', 'user.created_at as member_since', 'user.gender as user_gender',
                ])
            ->get();
            // $data['seller_other_deals'] =  $deal->sellerOtherDeals();
            // $data['category_other_deals'] =  $deal->categoryOtherDeals();

        return $this->successResponse($data);
    }

    public function categoryProjects(Category $category){
        $category = Category::where('categories.id', $category->id)->with('projects', 'children')->get();

        return $this->successResponse($category);

    }
    public function categoryDeals(Category $category){
        $category = Category::where('categories.id', $category->id)
            ->with('deals', 'children')->get();

        return $this->successResponse($category);
    }


    public function featuredDeals(){
        // $feature_projects = Project::where(['boosted' =>true, 'status'=> true])->inRandomOrder()->limit(2)->get();
        $categories_deals = Deal::where(['status'=> true])->get()->groupBy('category_id', true);
        // $latest_projects =  Project::where(['status'=> true])->inRandomOrder()->limit(2)->get();

        return $this->successResponse($categories_deals, 'Deals are grouped by category');
    }
}
