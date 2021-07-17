<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HelperController extends Controller
{
    public function mainCategories(){
        $categories = Category::whereHas('children')->where('status', true)->orderBy(DB::raw('ISNULL(position), position'), 'ASC')->orderBy('name', 'desc')->get();
        return $this->successResponse($categories->toArray(), 'Active categories');
    }

    public function subCategory(Category $category){
        return $this->successResponse($category->children->toArray(), 'sub categories');
    }
}
