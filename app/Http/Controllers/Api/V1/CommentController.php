<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Comments;
use App\Models\Updates;
use Illuminate\Http\Request;
use App\Http\Resources\CommentResource;

class CommentController extends BaseController
{
    /**
     * Get post comments
     */
    public function index($postId)
    {
        $post = Updates::find($postId);
        
        if (!$post) {
            return $this->notFoundResponse('Post not found');
        }

        $comments = Comments::where('updates_id', $postId)
            ->with('user:id,username,name,avatar')
            ->latest()
            ->paginate(50);

        return $this->paginatedResponse($comments);
    }

    /**
     * Create comment
     */
    public function store(Request $request, $postId)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $post = Updates::find($postId);
        
        if (!$post) {
            return $this->notFoundResponse('Post not found');
        }

        $comment = Comments::create([
            'updates_id' => $postId,
            'user_id' => auth()->id(),
            'reply' => $request->comment,
        ]);

        return $this->successResponse(
            new CommentResource($comment->load('user:id,username,name,avatar')),
            'Comment added successfully',
            201
        );
    }

    /**
     * Update comment
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $comment = Comments::find($id);
        
        if (!$comment) {
            return $this->notFoundResponse('Comment not found');
        }

        if ($comment->user_id != auth()->id()) {
            return $this->forbiddenResponse('You can only update your own comments');
        }

        $comment->update(['reply' => $request->comment]);

        return $this->successResponse(
            new CommentResource($comment),
            'Comment updated successfully'
        );
    }

    /**
     * Delete comment
     */
    public function destroy($id)
    {
        $comment = Comments::find($id);
        
        if (!$comment) {
            return $this->notFoundResponse('Comment not found');
        }

        if ($comment->user_id != auth()->id()) {
            return $this->forbiddenResponse('You can only delete your own comments');
        }

        $comment->delete();

        return $this->successResponse(null, 'Comment deleted successfully');
    }
}
