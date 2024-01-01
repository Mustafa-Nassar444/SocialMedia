<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Friendship;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendshipController extends Controller
{
    //

    public function sendFriendRequest(User $friend){
        $user = Auth::guard('api')->user();

        // Check if the friendship already exists
        $existingFriendship = Friendship::where('user_id', $user->id)
            ->where('friend_id', $friend->id)
            ->first();

        if ($existingFriendship) {
            return response()->json(['message' => 'Friend request already sent.'], 422);
        }
        if($user->id == $friend->id){
            return response()->json(['error' => 'Cannot send friend request to yourself.'], 400);
        }
        // Create a new friend request
        Friendship::create([
            'user_id' => $user->id,
            'friend_id' => $friend->id,

        ]);

        return response()->json(['message' => 'Friend request sent.']);

    }


    public function acceptFriendRequest($friendshipId){
        $friend=Auth::guard('api')->user();
        $friendship=Friendship::where('id',$friendshipId)
            ->where('friend_id',$friend->id)
            ->where('status','pending')
            ->first();
        if (!$friendship) {
            return response()->json(['message' => 'Friend request not found.'], 404);
        }
        $friendship->update(
            [
                'status'=>'accepted'
            ]
        );
        Friendship::create(
            [
                'user_id' => $friend->id,
                'friend_id' => $friendship->user_id,
                'status' => 'accepted',
            ]
        );
        return response()->json(['message' => 'Friend request accepted.']);

    }

    public function rejectFriendRequest($friendshipId){
        $user=Auth::guard('api')->user();
        $friendship=Friendship::where('id',$friendshipId)
            ->where('friend_id',$user->id)
            ->where('status','pending')
            ->first();
        if (!$friendship) {
            return response()->json(['message' => 'Friend request not found.'], 404);
        }
        $friendship->update(
            [
                'status'=>'rejected'
            ]
        );
        $friendship->delete($friendshipId);
        return response()->json(['message' => 'Friend request accepted.']);

    }


    public function getFriendRequestsReceived()
    {
        $user = Auth::guard('api')->user();

        $friendRequests = Friendship::where('friend_id', $user->id)
            ->where('status', 'pending')
            ->with('user')
            ->get();

        return response()->json(['friendRequestsReceived' => $friendRequests]);
    }
    public function getFriendList()
    {
        $user = Auth::guard('api')->user();

        $friends = Friendship::where('user_id', $user->id)
            ->where('status', 'accepted')
            ->with(['user', 'friend'])
            ->get();

        return response()->json(['friends' => $friends]);
    }

}
