<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Live;
use Illuminate\Http\Request;
use App\Http\Requests\Api\LiveRequest;
use App\Http\Resources\LiveResource;

class LiveController extends BaseController
{
    /**
     * Get all Lives
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $items = Live::latest()->paginate(20);
        
        return $this->paginatedResponse($items);
    }
    
    /**
     * Get single Live
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $item = Live::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Live not found');
        }
        
        return $this->successResponse(
            new LiveResource($item)
        );
    }
    
    /**
     * Create new Live
     * 
     * @param LiveRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(LiveRequest $request)
    {
        $item = Live::create($request->validated());
        
        return $this->successResponse(
            new LiveResource($item),
            'Live created successfully',
            201
        );
    }
    
    /**
     * Update Live
     * 
     * @param LiveRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(LiveRequest $request, $id)
    {
        $item = Live::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Live not found');
        }
        
        $item->update($request->validated());
        
        return $this->successResponse(
            new LiveResource($item),
            'Live updated successfully'
        );
    }
    
    /**
     * Delete Live
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $item = Live::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Live not found');
        }
        
        $item->delete();
        
        return $this->successResponse(null, 'Live deleted successfully');
    }

    /**
     * start endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function start(Request $request)
    {
        // TODO: Implement start logic
        return $this->successResponse(null, 'start endpoint');
    }

    /**
     * stop endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function stop(Request $request)
    {
        // TODO: Implement stop logic
        return $this->successResponse(null, 'stop endpoint');
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

    /**
     * join endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function join(Request $request)
    {
        // TODO: Implement join logic
        return $this->successResponse(null, 'join endpoint');
    }
}
