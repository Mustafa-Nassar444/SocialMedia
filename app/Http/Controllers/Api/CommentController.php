<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    //

    public function store(Request $request, Post $post)
    {
        $user=Auth::guard('api')->user();

        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $comment= Comment::create([
            'post_id'=>$post->id,
            'user_id'=>$user->id,
            'content'=>$request->input('content')
        ]);

        return response()->json(['message' => 'Comment Added',
            'Comment'=>$comment]);
    }

    public function update(Request $request, Post $post)
    {
        $user = Auth::guard('api')->user();

        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        // Update comment content$
        $comment=Comment::where('post_id',$post->id)
            ->where('user_id',$user->id)
            ->first();

        if(!$comment || $comment->user_id !== $user->id)
        {
            return response()->json(['error' => 'Unauthorized.'], 403);

        }

        $comment->update(['content' => $request->input('content')]);

        return response()->json(['message' => 'Comment updated successfully', 'comment' => $comment]);
    }


    public function destroy(Post $post)
    {
        $user=Auth::guard('api')->user();
        $comment=Comment::where('post_id',$post->id)
            ->where('user_id',$user->id)
            ->first();
        if (!$comment) {
            return response()->json(['message' => 'Comment not found.'], 404);
        }
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully.']);
    }
}
