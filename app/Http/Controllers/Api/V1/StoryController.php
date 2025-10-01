<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Story;
use Illuminate\Http\Request;
use App\Http\Requests\Api\StoryRequest;
use App\Http\Resources\StoryResource;

class StoryController extends BaseController
{
    /**
     * Get all Storys
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $items = Story::latest()->paginate(20);
        
        return $this->paginatedResponse($items);
    }
    
    /**
     * Get single Story
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $item = Story::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Story not found');
        }
        
        return $this->successResponse(
            new StoryResource($item)
        );
    }
    
    /**
     * Create new Story
     * 
     * @param StoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoryRequest $request)
    {
        $item = Story::create($request->validated());
        
        return $this->successResponse(
            new StoryResource($item),
            'Story created successfully',
            201
        );
    }
    
    /**
     * Update Story
     * 
     * @param StoryRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(StoryRequest $request, $id)
    {
        $item = Story::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Story not found');
        }
        
        $item->update($request->validated());
        
        return $this->successResponse(
            new StoryResource($item),
            'Story updated successfully'
        );
    }
    
    /**
     * Delete Story
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $item = Story::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Story not found');
        }
        
        $item->delete();
        
        return $this->successResponse(null, 'Story deleted successfully');
    }

    /**
     * view endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function view(Request $request)
    {
        // TODO: Implement view logic
        return $this->successResponse(null, 'view endpoint');
    }

    /**
     * viewers endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function viewers(Request $request)
    {
        // TODO: Implement viewers logic
        return $this->successResponse(null, 'viewers endpoint');
    }
}
