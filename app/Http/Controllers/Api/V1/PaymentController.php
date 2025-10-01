<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Requests\Api\PaymentRequest;
use App\Http\Resources\PaymentResource;

class PaymentController extends BaseController
{
    /**
     * Get all Payments
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $items = Payment::latest()->paginate(20);
        
        return $this->paginatedResponse($items);
    }
    
    /**
     * Get single Payment
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $item = Payment::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Payment not found');
        }
        
        return $this->successResponse(
            new PaymentResource($item)
        );
    }
    
    /**
     * Create new Payment
     * 
     * @param PaymentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PaymentRequest $request)
    {
        $item = Payment::create($request->validated());
        
        return $this->successResponse(
            new PaymentResource($item),
            'Payment created successfully',
            201
        );
    }
    
    /**
     * Update Payment
     * 
     * @param PaymentRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PaymentRequest $request, $id)
    {
        $item = Payment::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Payment not found');
        }
        
        $item->update($request->validated());
        
        return $this->successResponse(
            new PaymentResource($item),
            'Payment updated successfully'
        );
    }
    
    /**
     * Delete Payment
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $item = Payment::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Payment not found');
        }
        
        $item->delete();
        
        return $this->successResponse(null, 'Payment deleted successfully');
    }

    /**
     * tip endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function tip(Request $request)
    {
        // TODO: Implement tip logic
        return $this->successResponse(null, 'tip endpoint');
    }

    /**
     * ppv endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ppv(Request $request)
    {
        // TODO: Implement ppv logic
        return $this->successResponse(null, 'ppv endpoint');
    }

    /**
     * withdraw endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function withdraw(Request $request)
    {
        // TODO: Implement withdraw logic
        return $this->successResponse(null, 'withdraw endpoint');
    }

    /**
     * transactions endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transactions(Request $request)
    {
        // TODO: Implement transactions logic
        return $this->successResponse(null, 'transactions endpoint');
    }
}
