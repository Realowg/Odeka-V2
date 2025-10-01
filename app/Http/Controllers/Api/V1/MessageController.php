<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Requests\Api\MessageRequest;
use App\Http\Resources\MessageResource;

class MessageController extends BaseController
{
    /**
     * Get all Messages
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $items = Message::latest()->paginate(20);
        
        return $this->paginatedResponse($items);
    }
    
    /**
     * Get single Message
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $item = Message::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Message not found');
        }
        
        return $this->successResponse(
            new MessageResource($item)
        );
    }
    
    /**
     * Create new Message
     * 
     * @param MessageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(MessageRequest $request)
    {
        $item = Message::create($request->validated());
        
        return $this->successResponse(
            new MessageResource($item),
            'Message created successfully',
            201
        );
    }
    
    /**
     * Update Message
     * 
     * @param MessageRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(MessageRequest $request, $id)
    {
        $item = Message::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Message not found');
        }
        
        $item->update($request->validated());
        
        return $this->successResponse(
            new MessageResource($item),
            'Message updated successfully'
        );
    }
    
    /**
     * Delete Message
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $item = Message::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Message not found');
        }
        
        $item->delete();
        
        return $this->successResponse(null, 'Message deleted successfully');
    }

    /**
     * send endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(Request $request)
    {
        // TODO: Implement send logic
        return $this->successResponse(null, 'send endpoint');
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
     * unreadCount endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unreadCount(Request $request)
    {
        // TODO: Implement unreadCount logic
        return $this->successResponse(null, 'unreadCount endpoint');
    }
}
