<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NewsFeedController extends Controller
{
    public function getNewsFeed()
    {

    $user=User::find(Auth::guard('api')->user()->id);
    // Fetch the IDs of friends
    $friendIds = $user->friends()->pluck('user_id')->push($user->id);

    // Fetch recent posts from friends
    $posts = Post::whereIn('user_id', $friendIds)
        ->orderByDesc('created_at')
        ->paginate(10); // You can adjust the number of posts per page
    //$posts->load('photos','likes','comments');
        return PostResource::collection($posts);
    }


    public function sharePost( Post $post)
    {
        $user = Auth::guard('api')->user();



        // Share the post
        $share = new Post([
            'user_id' => $user->id,
            'content' => $post->content,
            'original_post_id' => $post->id,
        ]);
        $share->save();

        // Fetch the shared post without photos
        $sharedPost = Post::find($share->id);

        // Fetch the original post with photos
        $originalPost = Post::with('photos')->find($post->id);

        // Merge the original post's photos into the shared post
        $sharedPost->photos=$originalPost->photos;

        return response()->json(['post' => $sharedPost]);
    }

}
