<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Updates;
use App\Models\Like;
use App\Models\Bookmarks;
use App\Models\Reports;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Requests\Api\CreatePostRequest;
use App\Http\Requests\Api\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Events\NewPostEvent;

class PostController extends BaseController
{
    /**
     * Get user feed (timeline)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get subscribed creators
        $subscriptions = $user->userSubscriptions()
            ->where('stripe_status', 'active')
            ->pluck('stripe_id')
            ->toArray();

        // Add own posts
        $subscriptions[] = $user->id;

        // Get feed posts
        $posts = Updates::whereIn('user_id', $subscriptions)
            ->where('status', 'active')
            ->where(function($query) {
                $query->where('schedule', false)
                      ->orWhere(function($q) {
                          $q->where('schedule', true)
                            ->where('scheduled_date', '<=', now());
                      });
            })
            ->getSelectRelations()
            ->orderBy('date', 'desc')
            ->paginate(20);

        return $this->paginatedResponse($posts);
    }

    /**
     * Get single post
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $post = Updates::with(['creator:id,name,username,avatar,verified_id', 'media', 'likes', 'comments'])
            ->find($id);
        
        if (!$post) {
            return $this->notFoundResponse('Post not found');
        }

        // Check if post is locked and user has access
        if ($post->locked === 'yes' && $post->user_id != auth()->id()) {
            // Check subscription
            $hasSubscription = auth()->user()->userSubscriptions()
                ->where('stripe_id', $post->user_id)
                ->where('stripe_status', 'active')
                ->exists();

            if (!$hasSubscription) {
                return $this->forbiddenResponse('This post is locked. Subscription required.');
            }
        }

        return $this->successResponse(
            new PostResource($post)
        );
    }

    /**
     * Create a new post
     * 
     * @param CreatePostRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreatePostRequest $request)
    {
        $tokenId = Str::random(150);

        $post = Updates::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => auth()->id(),
            'token_id' => $tokenId,
            'locked' => $request->locked ? 'yes' : 'no',
            'price' => $request->price ?? 0,
            'status' => 'active',
            'schedule' => $request->has('scheduled_date'),
            'scheduled_date' => $request->scheduled_date,
            'ip' => $request->ip(),
        ]);

        // Trigger event for notifications
        event(new NewPostEvent($post));

        return $this->successResponse(
            new PostResource($post->load(['creator', 'media', 'likes', 'comments'])),
            'Post created successfully',
            201
        );
    }

    /**
     * Update a post
     * 
     * @param UpdatePostRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePostRequest $request, $id)
    {
        $post = Updates::find($id);
        
        if (!$post) {
            return $this->notFoundResponse('Post not found');
        }

        // Check if user owns this post
        if ($post->user_id != auth()->id()) {
            return $this->forbiddenResponse('You can only update your own posts');
        }

        $post->update([
            'title' => $request->title ?? $post->title,
            'description' => $request->description ?? $post->description,
            'locked' => $request->has('locked') ? ($request->locked ? 'yes' : 'no') : $post->locked,
            'price' => $request->price ?? $post->price,
        ]);

        return $this->successResponse(
            new PostResource($post->fresh(['creator', 'media', 'likes', 'comments'])),
            'Post updated successfully'
        );
    }

    /**
     * Delete a post
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $post = Updates::find($id);
        
        if (!$post) {
            return $this->notFoundResponse('Post not found');
        }

        // Check if user owns this post
        if ($post->user_id != auth()->id()) {
            return $this->forbiddenResponse('You can only delete your own posts');
        }

        // Soft delete by setting status to deleted
        $post->update(['status' => 'deleted']);

        return $this->successResponse(null, 'Post deleted successfully');
    }

    /**
     * Like a post
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function like($id)
    {
        $post = Updates::find($id);
        
        if (!$post) {
            return $this->notFoundResponse('Post not found');
        }

        // Check if already liked
        $existingLike = Like::where('user_id', auth()->id())
            ->where('updates_id', $id)
            ->first();

        if ($existingLike) {
            return $this->errorResponse('Post already liked', null, 400, 'ALREADY_LIKED');
        }

        $like = Like::create([
            'user_id' => auth()->id(),
            'updates_id' => $id,
            'status' => '1',
        ]);

        return $this->successResponse([
            'like_id' => $like->id,
            'total_likes' => $post->likes()->count(),
        ], 'Post liked successfully');
    }

    /**
     * Unlike a post
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function unlike($id)
    {
        $like = Like::where('user_id', auth()->id())
            ->where('updates_id', $id)
            ->first();

        if (!$like) {
            return $this->notFoundResponse('Like not found');
        }

        $like->delete();

        $post = Updates::find($id);

        return $this->successResponse([
            'total_likes' => $post ? $post->likes()->count() : 0,
        ], 'Post unliked successfully');
    }

    /**
     * Get post likes
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function likes($id)
    {
        $post = Updates::find($id);
        
        if (!$post) {
            return $this->notFoundResponse('Post not found');
        }

        $likes = Like::where('updates_id', $id)
            ->with('user:id,username,name,avatar')
            ->latest()
            ->paginate(50);

        return $this->paginatedResponse($likes);
    }

    /**
     * Bookmark a post
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function bookmark($id)
    {
        $post = Updates::find($id);
        
        if (!$post) {
            return $this->notFoundResponse('Post not found');
        }

        // Check if already bookmarked
        $existingBookmark = Bookmarks::where('user_id', auth()->id())
            ->where('updates_id', $id)
            ->first();

        if ($existingBookmark) {
            return $this->errorResponse('Post already bookmarked', null, 400, 'ALREADY_BOOKMARKED');
        }

        $bookmark = Bookmarks::create([
            'user_id' => auth()->id(),
            'updates_id' => $id,
        ]);

        return $this->successResponse([
            'bookmark_id' => $bookmark->id,
        ], 'Post bookmarked successfully');
    }

    /**
     * Remove bookmark
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function unbookmark($id)
    {
        $bookmark = Bookmarks::where('user_id', auth()->id())
            ->where('updates_id', $id)
            ->first();

        if (!$bookmark) {
            return $this->notFoundResponse('Bookmark not found');
        }

        $bookmark->delete();

        return $this->successResponse(null, 'Bookmark removed successfully');
    }

    /**
     * Get user's bookmarks
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function bookmarks()
    {
        $bookmarks = Bookmarks::where('user_id', auth()->id())
            ->with(['post' => function($query) {
                $query->with(['creator:id,name,username,avatar', 'media', 'likes', 'comments']);
            }])
            ->latest()
            ->paginate(20);

        $posts = $bookmarks->map(function($bookmark) {
            return $bookmark->post;
        })->filter();

        return $this->paginatedResponse($bookmarks);
    }

    /**
     * Report a post
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function report(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $post = Updates::find($id);
        
        if (!$post) {
            return $this->notFoundResponse('Post not found');
        }

        // Check if already reported
        $existingReport = Reports::where('user_id', auth()->id())
            ->where('updates_id', $id)
            ->first();

        if ($existingReport) {
            return $this->errorResponse('Post already reported', null, 400, 'ALREADY_REPORTED');
        }

        $report = Reports::create([
            'user_id' => auth()->id(),
            'updates_id' => $id,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return $this->successResponse([
            'report_id' => $report->id,
        ], 'Post reported successfully');
    }
}
