<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Requests\Api\AdminRequest;
use App\Http\Resources\AdminResource;

class AdminController extends BaseController
{
    /**
     * Get all Admins
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $items = Admin::latest()->paginate(20);
        
        return $this->paginatedResponse($items);
    }
    
    /**
     * Get single Admin
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $item = Admin::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Admin not found');
        }
        
        return $this->successResponse(
            new AdminResource($item)
        );
    }
    
    /**
     * Create new Admin
     * 
     * @param AdminRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AdminRequest $request)
    {
        $item = Admin::create($request->validated());
        
        return $this->successResponse(
            new AdminResource($item),
            'Admin created successfully',
            201
        );
    }
    
    /**
     * Update Admin
     * 
     * @param AdminRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(AdminRequest $request, $id)
    {
        $item = Admin::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Admin not found');
        }
        
        $item->update($request->validated());
        
        return $this->successResponse(
            new AdminResource($item),
            'Admin updated successfully'
        );
    }
    
    /**
     * Delete Admin
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $item = Admin::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Admin not found');
        }
        
        $item->delete();
        
        return $this->successResponse(null, 'Admin deleted successfully');
    }

    /**
     * dashboard endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboard(Request $request)
    {
        // TODO: Implement dashboard logic
        return $this->successResponse(null, 'dashboard endpoint');
    }

    /**
     * users endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function users(Request $request)
    {
        // TODO: Implement users logic
        return $this->successResponse(null, 'users endpoint');
    }

    /**
     * reports endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reports(Request $request)
    {
        // TODO: Implement reports logic
        return $this->successResponse(null, 'reports endpoint');
    }

    /**
     * analytics endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function analytics(Request $request)
    {
        // TODO: Implement analytics logic
        return $this->successResponse(null, 'analytics endpoint');
    }
}
