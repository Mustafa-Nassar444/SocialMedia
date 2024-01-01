<?php

namespace App\Http\Middleware;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Profile;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next,$type)
    {
        $user = Auth::guard('api')->user();

        switch ($type) {
            case 'post':
                $postId = $request->route('post');
                $post = Post::find($postId)->first();

                if (!$post || $post->user_id !== $user->id) {
                    return response()->json(['error' => 'Unauthorized.'], 403);
                }
                break;



            default:
                return response()->json(['error' => 'Invalid type.'], 400);
        }

        return $next($request);
    }
}
