<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Http\Requests\Api\SubscriptionRequest;
use App\Http\Resources\SubscriptionResource;

class SubscriptionController extends BaseController
{
    /**
     * Get all Subscriptions
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $items = Subscription::latest()->paginate(20);
        
        return $this->paginatedResponse($items);
    }
    
    /**
     * Get single Subscription
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $item = Subscription::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Subscription not found');
        }
        
        return $this->successResponse(
            new SubscriptionResource($item)
        );
    }
    
    /**
     * Create new Subscription
     * 
     * @param SubscriptionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SubscriptionRequest $request)
    {
        $item = Subscription::create($request->validated());
        
        return $this->successResponse(
            new SubscriptionResource($item),
            'Subscription created successfully',
            201
        );
    }
    
    /**
     * Update Subscription
     * 
     * @param SubscriptionRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SubscriptionRequest $request, $id)
    {
        $item = Subscription::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Subscription not found');
        }
        
        $item->update($request->validated());
        
        return $this->successResponse(
            new SubscriptionResource($item),
            'Subscription updated successfully'
        );
    }
    
    /**
     * Delete Subscription
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $item = Subscription::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Subscription not found');
        }
        
        $item->delete();
        
        return $this->successResponse(null, 'Subscription deleted successfully');
    }

    /**
     * cancel endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(Request $request)
    {
        // TODO: Implement cancel logic
        return $this->successResponse(null, 'cancel endpoint');
    }

    /**
     * renew endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function renew(Request $request)
    {
        // TODO: Implement renew logic
        return $this->successResponse(null, 'renew endpoint');
    }
}
