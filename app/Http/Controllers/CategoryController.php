<?php

namespace App\Http\Controllers;

use App\Helpers\AboutUser;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(
        ResponseService $responseService
    )
    {
        $data = Category::all();
        return $responseService->successfullGetted($data->toArray());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        ResponseService $responseService,
        AboutUser $aboutUser,
        CategoryRequest $categoryRequest,
        Category $category,
    )
    {
        // verify the user permission
        if(!$aboutUser->isPermisToCreate($category))
            return $responseService->notAuthorized();

        // record the datas
        $category = new Category();
        $category->name = $categoryRequest->name;
        $category->infos = $categoryRequest->infos;
        $category->created_by = $aboutUser->id();

        if (
            Category::where('date', $category->date)
                ->where('name', $category->category_id)
                ->where('infos', $category->place_id)
                ->exists()
        ) 
            return $responseService->alreadyExist('Category');

        // send th response
        if($category->save())
            return $responseService->successfullStored('Category');

        return $responseService->errorServer();
    }

    /**
     * Display the specified resource.
     */
    public function show(
        Category $category,
        ResponseService $responseService
    )
    {
        return $responseService->successfullGetted(
            $category->toArray(), 'Category'
        );     
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        CategoryRequest $categoryRequest,
        ResponseService $responseService,
        AboutUser $aboutUser,
        Category $category
    )
    {
        if(!$aboutUser->isPermisToInteract($category))
            return $responseService->notAuthorized();

        $category->name = $categoryRequest->name;
        $category->infos = $categoryRequest->infos;
        $category->updated_by = $aboutUser->id();

        if (
            Category::where('date', $category->date)
                ->where('name', $category->category_id)
                ->where('infos', $category->place_id)
                ->exists()
        ) 
            return $responseService->alreadyExist('Category');

        if($category->update())
            return $responseService->successfullUpdated('Category');
        
        return $responseService->errorServer();
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        ResponseService $responseService,
        AboutUser $aboutUser,
        Category $category)
    {
        if(!$aboutUser->isPermisToInteract($category))
            return $responseService->notAuthorized();
        
        if($category->delete())
            return $responseService->successfullDeleted('Category');

    }
}
