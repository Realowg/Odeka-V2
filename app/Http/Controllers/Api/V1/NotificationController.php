<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Notifications;
use Illuminate\Http\Request;

class NotificationController extends BaseController
{
    public function index()
    {
        $notifications = Notifications::where('destination', auth()->id())
            ->with('author:id,username,name,avatar')
            ->latest()
            ->paginate(50);

        return $this->paginatedResponse($notifications);
    }

    public function unread()
    {
        $notifications = Notifications::where('destination', auth()->id())
            ->where('status', 0)
            ->with('author:id,username,name,avatar')
            ->latest()
            ->paginate(50);

        return $this->paginatedResponse($notifications);
    }

    public function markRead($id)
    {
        $notification = Notifications::find($id);
        
        if (!$notification || $notification->destination != auth()->id()) {
            return $this->notFoundResponse('Notification not found');
        }

        $notification->update(['status' => 1]);

        return $this->successResponse(null, 'Notification marked as read');
    }

    public function readAll()
    {
        Notifications::where('destination', auth()->id())
            ->where('status', 0)
            ->update(['status' => 1]);

        return $this->successResponse(null, 'All notifications marked as read');
    }

    public function destroy($id)
    {
        $notification = Notifications::find($id);
        
        if (!$notification || $notification->destination != auth()->id()) {
            return $this->notFoundResponse('Notification not found');
        }

        $notification->delete();

        return $this->successResponse(null, 'Notification deleted');
    }

    public function preferences(Request $request)
    {
        $user = auth()->user();
        
        $user->update($request->only([
            'notify_new_subscriber',
            'notify_new_tip',
            'notify_new_ppv',
            'notify_liked_post',
            'notify_commented_post',
            'notify_new_post',
            'notify_live_streaming',
            'notify_mentions',
        ]));

        return $this->successResponse($user->only([
            'notify_new_subscriber',
            'notify_new_tip',
            'notify_new_ppv',
            'notify_liked_post',
            'notify_commented_post',
            'notify_new_post',
            'notify_live_streaming',
            'notify_mentions',
        ]), 'Preferences updated');
    }

    public function registerDevice(Request $request)
    {
        $request->validate([
            'device_token' => 'required|string',
        ]);

        auth()->user()->update([
            'device_token' => $request->device_token,
        ]);

        return $this->successResponse(null, 'Device registered for push notifications');
    }
}
