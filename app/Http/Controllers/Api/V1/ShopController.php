<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Shop;
use Illuminate\Http\Request;
use App\Http\Requests\Api\ShopRequest;
use App\Http\Resources\ShopResource;

class ShopController extends BaseController
{
    /**
     * Get all Shops
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $items = Shop::latest()->paginate(20);
        
        return $this->paginatedResponse($items);
    }
    
    /**
     * Get single Shop
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $item = Shop::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Shop not found');
        }
        
        return $this->successResponse(
            new ShopResource($item)
        );
    }
    
    /**
     * Create new Shop
     * 
     * @param ShopRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ShopRequest $request)
    {
        $item = Shop::create($request->validated());
        
        return $this->successResponse(
            new ShopResource($item),
            'Shop created successfully',
            201
        );
    }
    
    /**
     * Update Shop
     * 
     * @param ShopRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ShopRequest $request, $id)
    {
        $item = Shop::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Shop not found');
        }
        
        $item->update($request->validated());
        
        return $this->successResponse(
            new ShopResource($item),
            'Shop updated successfully'
        );
    }
    
    /**
     * Delete Shop
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $item = Shop::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Shop not found');
        }
        
        $item->delete();
        
        return $this->successResponse(null, 'Shop deleted successfully');
    }

    /**
     * products endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function products(Request $request)
    {
        // TODO: Implement products logic
        return $this->successResponse(null, 'products endpoint');
    }

    /**
     * orders endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function orders(Request $request)
    {
        // TODO: Implement orders logic
        return $this->successResponse(null, 'orders endpoint');
    }
}
