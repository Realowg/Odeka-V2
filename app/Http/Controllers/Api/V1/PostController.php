<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Requests\Api\PostRequest;
use App\Http\Resources\PostResource;

class PostController extends BaseController
{
    /**
     * Get all Posts
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $items = Post::latest()->paginate(20);
        
        return $this->paginatedResponse($items);
    }
    
    /**
     * Get single Post
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $item = Post::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Post not found');
        }
        
        return $this->successResponse(
            new PostResource($item)
        );
    }
    
    /**
     * Create new Post
     * 
     * @param PostRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PostRequest $request)
    {
        $item = Post::create($request->validated());
        
        return $this->successResponse(
            new PostResource($item),
            'Post created successfully',
            201
        );
    }
    
    /**
     * Update Post
     * 
     * @param PostRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PostRequest $request, $id)
    {
        $item = Post::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Post not found');
        }
        
        $item->update($request->validated());
        
        return $this->successResponse(
            new PostResource($item),
            'Post updated successfully'
        );
    }
    
    /**
     * Delete Post
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $item = Post::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Post not found');
        }
        
        $item->delete();
        
        return $this->successResponse(null, 'Post deleted successfully');
    }

    /**
     * like endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function like(Request $request)
    {
        // TODO: Implement like logic
        return $this->successResponse(null, 'like endpoint');
    }

    /**
     * unlike endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unlike(Request $request)
    {
        // TODO: Implement unlike logic
        return $this->successResponse(null, 'unlike endpoint');
    }

    /**
     * bookmark endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bookmark(Request $request)
    {
        // TODO: Implement bookmark logic
        return $this->successResponse(null, 'bookmark endpoint');
    }

    /**
     * report endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function report(Request $request)
    {
        // TODO: Implement report logic
        return $this->successResponse(null, 'report endpoint');
    }
}
