<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    //

    public function likePost(Post $post){
            $user=Auth::guard('api')->user();
            $checkLike=Like::where('user_id',$user->id)
                ->where('post_id',$post->id)
                ->first();
            if($checkLike)
            {
                return response()->json(['message'=>'Post already liked']);
            }
            $like=new Like([
                'user_id'=>$user->id,
                'post_id'=>$post->id
            ]);

            $like->save();

        return response()->json(['message'=>'Post liked']);
    }

    public function unLikePost(Post $post){
        try{
            $user = Auth::guard('api')->user();
            $like = Like::where('user_id', $user->id)
                ->where('post_id', $post->id)
                ->first();
            if (!$like) {
                return response()->json(['message' => 'Post not liked']);
            }

            $like->delete();
            return response()->json(['message' => 'Post unLiked successfully']);
        }
        catch (\Exception $e){
            return response()->json(['message' => $e->getMessage()]);

        }
    }
}
