<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Messages;
use App\Models\Conversations;
use App\Models\MediaMessages;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Api\SendMessageRequest;
use App\Http\Resources\MessageResource;
use App\Http\Resources\ConversationResource;

class MessageController extends BaseController
{
    /**
     * Get all conversations
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $conversations = Messages::conversations();
        
        return $this->paginatedResponse($conversations);
    }
    
    /**
     * Get conversation with specific user
     * 
     * @param int $userId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($userId, Request $request)
    {
        $user = User::find($userId);
        
        if (!$user) {
            return $this->notFoundResponse('User not found');
        }

        $skip = $request->get('skip', 0);
        $messages = Messages::getMessageChat($userId, $skip);
        
        return $this->successResponse([
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
                'avatar' => $user->avatar ? asset('avatar/' . $user->avatar) : null,
            ],
            'messages' => MessageResource::collection($messages),
        ]);
    }
    
    /**
     * Send a message
     * 
     * @param SendMessageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(SendMessageRequest $request)
    {
        $recipient = User::find($request->to_user_id);
        
        if (!$recipient) {
            return $this->notFoundResponse('Recipient not found');
        }

        // Check if conversation exists
        $conversation = Conversations::where(function($query) use ($request) {
            $query->where('user_1', auth()->id())
                  ->where('user_2', $request->to_user_id);
        })->orWhere(function($query) use ($request) {
            $query->where('user_1', $request->to_user_id)
                  ->where('user_2', auth()->id());
        })->first();

        // Create conversation if it doesn't exist
        if (!$conversation) {
            $conversation = Conversations::create([
                'user_1' => auth()->id(),
                'user_2' => $request->to_user_id,
            ]);
        }

        // Create message
        $message = Messages::create([
            'conversations_id' => $conversation->id,
            'from_user_id' => auth()->id(),
            'to_user_id' => $request->to_user_id,
            'message' => $request->message ?? '',
            'price' => $request->price ?? 0,
            'tip' => $request->tip ?? 'no',
            'tip_amount' => $request->tip_amount ?? 0,
            'mode' => 'active',
            'status' => 'new',
        ]);

        // Handle media attachments if present
        if ($request->has('media')) {
            foreach ($request->media as $mediaItem) {
                MediaMessages::create([
                    'messages_id' => $message->id,
                    'user_id' => auth()->id(),
                    'type' => $mediaItem['type'] ?? 'image',
                    'image' => $mediaItem['type'] === 'image' ? $mediaItem['file'] : '',
                    'video' => $mediaItem['type'] === 'video' ? $mediaItem['file'] : '',
                    'music' => $mediaItem['type'] === 'audio' ? $mediaItem['file'] : '',
                    'file' => $mediaItem['file'] ?? '',
                    'token' => $mediaItem['token'] ?? '',
                    'status' => 'active',
                ]);
            }
        }

        $message->load(['sender:id,username,name,avatar', 'receiver:id,username,name,avatar', 'media']);

        return $this->successResponse(
            new MessageResource($message),
            'Message sent successfully',
            201
        );
    }
    
    /**
     * Delete a message
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $message = Messages::find($id);
        
        if (!$message) {
            return $this->notFoundResponse('Message not found');
        }

        // Check if user is sender
        if ($message->from_user_id != auth()->id()) {
            return $this->forbiddenResponse('You can only delete your own messages');
        }

        $message->update(['mode' => 'pending']);
        
        return $this->successResponse(null, 'Message deleted successfully');
    }

    /**
     * Mark message as read
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markRead($id)
    {
        $message = Messages::find($id);
        
        if (!$message) {
            return $this->notFoundResponse('Message not found');
        }

        // Check if user is the recipient
        if ($message->to_user_id != auth()->id()) {
            return $this->forbiddenResponse('You can only mark your own messages as read');
        }

        $message->update(['status' => 'readed']);
        
        return $this->successResponse(null, 'Message marked as read');
    }

    /**
     * Mark all messages from a user as read
     * 
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllRead($userId)
    {
        Messages::where('from_user_id', $userId)
            ->where('to_user_id', auth()->id())
            ->where('status', 'new')
            ->update(['status' => 'readed']);
        
        return $this->successResponse(null, 'All messages marked as read');
    }

    /**
     * Get unread message count
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function unreadCount()
    {
        $count = Messages::where('to_user_id', auth()->id())
            ->where('status', 'new')
            ->where('mode', 'active')
            ->count();
        
        return $this->successResponse([
            'unread_count' => $count
        ]);
    }

    /**
     * Get unread count per conversation
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function unreadCountByUser()
    {
        $unreadCounts = Messages::where('to_user_id', auth()->id())
            ->where('status', 'new')
            ->where('mode', 'active')
            ->select('from_user_id', DB::raw('count(*) as count'))
            ->groupBy('from_user_id')
            ->get()
            ->pluck('count', 'from_user_id');
        
        return $this->successResponse([
            'unread_by_user' => $unreadCounts
        ]);
    }
}
