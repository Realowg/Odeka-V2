<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Requests\Api\CommentRequest;
use App\Http\Resources\CommentResource;

class CommentController extends BaseController
{
    /**
     * Get all Comments
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $items = Comment::latest()->paginate(20);
        
        return $this->paginatedResponse($items);
    }
    
    /**
     * Get single Comment
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $item = Comment::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Comment not found');
        }
        
        return $this->successResponse(
            new CommentResource($item)
        );
    }
    
    /**
     * Create new Comment
     * 
     * @param CommentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CommentRequest $request)
    {
        $item = Comment::create($request->validated());
        
        return $this->successResponse(
            new CommentResource($item),
            'Comment created successfully',
            201
        );
    }
    
    /**
     * Update Comment
     * 
     * @param CommentRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CommentRequest $request, $id)
    {
        $item = Comment::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Comment not found');
        }
        
        $item->update($request->validated());
        
        return $this->successResponse(
            new CommentResource($item),
            'Comment updated successfully'
        );
    }
    
    /**
     * Delete Comment
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $item = Comment::find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Comment not found');
        }
        
        $item->delete();
        
        return $this->successResponse(null, 'Comment deleted successfully');
    }
}
