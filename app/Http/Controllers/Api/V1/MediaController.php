<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Media;
use Illuminate\Http\Request;
use App\Http\Requests\Api\MediaRequest;
use App\Http\Resources\MediaResource;

class MediaController extends BaseController
{
    /**
     * Get all Medias
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $items = Media::latest()->paginate(20);
        
        return $this->paginatedResponse($items);
    }
    
    /**
     * Get single Media
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $item = Media::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Media not found');
        }
        
        return $this->successResponse(
            new MediaResource($item)
        );
    }
    
    /**
     * Create new Media
     * 
     * @param MediaRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(MediaRequest $request)
    {
        $item = Media::create($request->validated());
        
        return $this->successResponse(
            new MediaResource($item),
            'Media created successfully',
            201
        );
    }
    
    /**
     * Update Media
     * 
     * @param MediaRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(MediaRequest $request, $id)
    {
        $item = Media::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Media not found');
        }
        
        $item->update($request->validated());
        
        return $this->successResponse(
            new MediaResource($item),
            'Media updated successfully'
        );
    }
    
    /**
     * Delete Media
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $item = Media::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Media not found');
        }
        
        $item->delete();
        
        return $this->successResponse(null, 'Media deleted successfully');
    }

    /**
     * upload endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        // TODO: Implement upload logic
        return $this->successResponse(null, 'upload endpoint');
    }

    /**
     * download endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function download(Request $request)
    {
        // TODO: Implement download logic
        return $this->successResponse(null, 'download endpoint');
    }

    /**
     * encode endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function encode(Request $request)
    {
        // TODO: Implement encode logic
        return $this->successResponse(null, 'encode endpoint');
    }
}
