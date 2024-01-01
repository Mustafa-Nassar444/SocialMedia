<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\PostPhotos;
use App\Services\PostService\StorePostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use function Termwind\renderUsing;

class PostController extends Controller
{

    //
    public function store(PostRequest $request){
        $user = Auth::guard('api')->user();

        return (new StorePostService())->store($request,$user);

    }
    public function show(){
        $posts = Post::all();
        $posts->load('photos','likes','comments');
        return response()->json([
            "posts" => $posts
        ]);
    }

    public function update(PostRequest $request,Post $post)
    {
        return (new StorePostService())->update($request,$post);
    }

    public function delete(Post $post){
        return (new StorePostService())->delete($post);
    }
}
