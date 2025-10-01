<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Odeva;
use Illuminate\Http\Request;
use App\Http\Requests\Api\OdevaRequest;
use App\Http\Resources\OdevaResource;

class OdevaController extends BaseController
{
    /**
     * Get all Odevas
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $items = Odeva::latest()->paginate(20);
        
        return $this->paginatedResponse($items);
    }
    
    /**
     * Get single Odeva
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $item = Odeva::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Odeva not found');
        }
        
        return $this->successResponse(
            new OdevaResource($item)
        );
    }
    
    /**
     * Create new Odeva
     * 
     * @param OdevaRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(OdevaRequest $request)
    {
        $item = Odeva::create($request->validated());
        
        return $this->successResponse(
            new OdevaResource($item),
            'Odeva created successfully',
            201
        );
    }
    
    /**
     * Update Odeva
     * 
     * @param OdevaRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(OdevaRequest $request, $id)
    {
        $item = Odeva::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Odeva not found');
        }
        
        $item->update($request->validated());
        
        return $this->successResponse(
            new OdevaResource($item),
            'Odeva updated successfully'
        );
    }
    
    /**
     * Delete Odeva
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $item = Odeva::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Odeva not found');
        }
        
        $item->delete();
        
        return $this->successResponse(null, 'Odeva deleted successfully');
    }

    /**
     * chat endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function chat(Request $request)
    {
        // TODO: Implement chat logic
        return $this->successResponse(null, 'chat endpoint');
    }

    /**
     * functions endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function functions(Request $request)
    {
        // TODO: Implement functions logic
        return $this->successResponse(null, 'functions endpoint');
    }

    /**
     * execute endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function execute(Request $request)
    {
        // TODO: Implement execute logic
        return $this->successResponse(null, 'execute endpoint');
    }

    /**
     * context endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function context(Request $request)
    {
        // TODO: Implement context logic
        return $this->successResponse(null, 'context endpoint');
    }

    /**
     * automation endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function automation(Request $request)
    {
        // TODO: Implement automation logic
        return $this->successResponse(null, 'automation endpoint');
    }

    /**
     * subscribe endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribe(Request $request)
    {
        // TODO: Implement subscribe logic
        return $this->successResponse(null, 'subscribe endpoint');
    }
}
