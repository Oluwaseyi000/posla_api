<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\CartRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;

class CategoryController extends Controller
{
    public function listCategories(){
        $data = Category::paginate(request()->per_page ?? $this::PER_PAGE);
        return $this->successResponse($data);
    }

    public function createCategory(CategoryRequest $request){
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);
        $data['status'] = $request->status ?? 1;
        $category = Category::create($data);
        return $this->successResponse($category);
    }

    public function updateCategory(CategoryRequest $request, Category $category){
        $category->update($request->validated());
        return $this->successResponse($category);
    }

    public function viewCategory(Category $category){
        return $this->successResponse($category);
    }

    public function childrenCategory(Category $category){
        return $this->successResponse($category->children);
    }

    public function deleteCategory(Category $category){
        if($category->children || $category->deals || $category->projects){
           return $this->errorResponse("Can't delete, relationships already exist");
        }
        $category->delete();

        return $this->successResponse();
    }
}
