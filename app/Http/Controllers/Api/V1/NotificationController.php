<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Requests\Api\NotificationRequest;
use App\Http\Resources\NotificationResource;

class NotificationController extends BaseController
{
    /**
     * Get all Notifications
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $items = Notification::latest()->paginate(20);
        
        return $this->paginatedResponse($items);
    }
    
    /**
     * Get single Notification
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $item = Notification::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Notification not found');
        }
        
        return $this->successResponse(
            new NotificationResource($item)
        );
    }
    
    /**
     * Create new Notification
     * 
     * @param NotificationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(NotificationRequest $request)
    {
        $item = Notification::create($request->validated());
        
        return $this->successResponse(
            new NotificationResource($item),
            'Notification created successfully',
            201
        );
    }
    
    /**
     * Update Notification
     * 
     * @param NotificationRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(NotificationRequest $request, $id)
    {
        $item = Notification::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Notification not found');
        }
        
        $item->update($request->validated());
        
        return $this->successResponse(
            new NotificationResource($item),
            'Notification updated successfully'
        );
    }
    
    /**
     * Delete Notification
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $item = Notification::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Notification not found');
        }
        
        $item->delete();
        
        return $this->successResponse(null, 'Notification deleted successfully');
    }

    /**
     * markRead endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markRead(Request $request)
    {
        // TODO: Implement markRead logic
        return $this->successResponse(null, 'markRead endpoint');
    }

    /**
     * readAll endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function readAll(Request $request)
    {
        // TODO: Implement readAll logic
        return $this->successResponse(null, 'readAll endpoint');
    }

    /**
     * preferences endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function preferences(Request $request)
    {
        // TODO: Implement preferences logic
        return $this->successResponse(null, 'preferences endpoint');
    }
}
