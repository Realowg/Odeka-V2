<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Followers;
use App\Models\RestrictedUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Api\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserDetailResource;
use App\Http\Resources\UserStatsResource;

class UserController extends BaseController
{
    /**
     * Get authenticated user
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        return $this->successResponse(
            new UserDetailResource($request->user())
        );
    }

    /**
     * Update authenticated user
     * 
     * @param UpdateUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request)
    {
        $user = $request->user();

        $updateData = $request->only([
            'name', 'location', 'website', 'profession',
            'facebook', 'twitter', 'instagram', 'youtube',
            'tiktok', 'pinterest', 'language', 'dark_mode'
        ]);

        // Map 'bio' to 'story' field
        if ($request->has('bio')) {
            $updateData['story'] = $request->bio;
        }

        // Map 'location' to 'city' field
        if ($request->has('location')) {
            $updateData['city'] = $request->location;
            unset($updateData['location']);
        }

        $user->update($updateData);

        return $this->successResponse(
            new UserDetailResource($user->fresh()),
            'Profile updated successfully'
        );
    }

    /**
     * Delete authenticated user account
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $user = $request->user();

        // Revoke all tokens
        $user->tokens()->delete();

        // Delete user account
        $user->delete();

        return $this->successResponse(null, 'Account deleted successfully');
    }

    /**
     * Get user by username
     * 
     * @param string $username
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($username)
    {
        $user = User::where('username', $username)
            ->where('status', 'active')
            ->first();

        if (!$user) {
            return $this->notFoundResponse('User not found');
        }

        return $this->successResponse(
            new UserDetailResource($user)
        );
    }

    /**
     * Get user posts
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function posts($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->notFoundResponse('User not found');
        }

        $posts = $user->posts()
            ->where('status', 'active')
            ->latest()
            ->paginate(20);

        return $this->paginatedResponse($posts);
    }

    /**
     * Get user statistics
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->notFoundResponse('User not found');
        }

        return $this->successResponse(
            new UserStatsResource($user)
        );
    }

    /**
     * Follow a user
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function follow(Request $request, $id)
    {
        $userToFollow = User::find($id);

        if (!$userToFollow) {
            return $this->notFoundResponse('User not found');
        }

        if ($id == $request->user()->id) {
            return $this->errorResponse('You cannot follow yourself', null, 400, 'SELF_FOLLOW');
        }

        // Check if already following
        $existingFollow = Followers::where('follower', $request->user()->id)
            ->where('following', $id)
            ->first();

        if ($existingFollow) {
            return $this->errorResponse('Already following this user', null, 400, 'ALREADY_FOLLOWING');
        }

        // Create follow relationship
        Followers::create([
            'follower' => $request->user()->id,
            'following' => $id,
        ]);

        return $this->successResponse(null, 'User followed successfully');
    }

    /**
     * Unfollow a user
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function unfollow(Request $request, $id)
    {
        $follow = Followers::where('follower', $request->user()->id)
            ->where('following', $id)
            ->first();

        if (!$follow) {
            return $this->notFoundResponse('Not following this user');
        }

        $follow->delete();

        return $this->successResponse(null, 'User unfollowed successfully');
    }

    /**
     * Get user followers
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function followers($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->notFoundResponse('User not found');
        }

        $followers = Followers::where('following', $id)
            ->with('followerUser:id,username,name,avatar')
            ->paginate(50);

        $data = $followers->map(function($follow) {
            return new UserResource($follow->followerUser);
        });

        return $this->paginatedResponse($followers);
    }

    /**
     * Get users that the user is following
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function following($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->notFoundResponse('User not found');
        }

        $following = Followers::where('follower', $id)
            ->with('followingUser:id,username,name,avatar')
            ->paginate(50);

        $data = $following->map(function($follow) {
            return new UserResource($follow->followingUser);
        });

        return $this->paginatedResponse($following);
    }

    /**
     * Restrict a user
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restrict(Request $request, $id)
    {
        $userToRestrict = User::find($id);

        if (!$userToRestrict) {
            return $this->notFoundResponse('User not found');
        }

        if ($id == $request->user()->id) {
            return $this->errorResponse('You cannot restrict yourself', null, 400, 'SELF_RESTRICT');
        }

        // Check if already restricted
        $existingRestriction = RestrictedUsers::where('user_id', $request->user()->id)
            ->where('restricted_user_id', $id)
            ->first();

        if ($existingRestriction) {
            return $this->errorResponse('User already restricted', null, 400, 'ALREADY_RESTRICTED');
        }

        // Create restriction
        RestrictedUsers::create([
            'user_id' => $request->user()->id,
            'restricted_user_id' => $id,
        ]);

        return $this->successResponse(null, 'User restricted successfully');
    }

    /**
     * Unrestrict a user
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function unrestrict(Request $request, $id)
    {
        $restriction = RestrictedUsers::where('user_id', $request->user()->id)
            ->where('restricted_user_id', $id)
            ->first();

        if (!$restriction) {
            return $this->notFoundResponse('User not restricted');
        }

        $restriction->delete();

        return $this->successResponse(null, 'User unrestricted successfully');
    }
}

