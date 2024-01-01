<?php

namespace App\Services\PostService;

use App\Models\Post;
use App\Models\PostPhotos;
use App\Traits\UploadImageTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StorePostService
{
    use UploadImageTrait;

    protected $model;
    public function __construct()
    {
        $this->model=new Post();
    }


    public function storePost($request,$user){

        $data=$request->except('photos');
        $data['user_id']=$user->id;
        $post=Post::create($data);
        return $post;
    }

    public function updatePost($request, $post){
        $data=$request->except('photos');
        $post->update($data);
        return $post;
    }

    public function store($request,$user){
        try{
            DB::beginTransaction();
            $post=$this->storePost($request,$user);


            // Handle photos only if new photos are being uploaded
            if($request->hasFile('photos')){

                // Store the photos
                $this->storePhotos($request,$post->id);
            }
            DB::commit();
            return response()->json([
                'message'=>'Post added successfully',
            ]);
        }
        catch (\Exception $e){
            DB::rollBack();
            $errorMessage = 'Post creation failed: ' . $e->getMessage();
            Log::error($errorMessage);
            return response()->json(['error' => 'Failed to create post. Please try again.'], 500);
        }


    }

    public function update($request, $post)
    {
        try {
            DB::beginTransaction();

            // Store the old photos for comparison
            $oldPhotos = $post->photos->pluck('photo')->toArray();

            // Update post details
            $this->updatePost($request, $post);

            // Handle photos only if new photos are being uploaded
            if ($request->hasFile('photos')) {
                // Store the new photos
                $this->storePhotos($request, $post->id);
            }
                // Remove old photos not present in the new request

                $removedPhotos = array_diff($oldPhotos, $request->input('removed_photos', []));

                // Delete old photos from database and storage
                PostPhotos::whereIn('photo', $removedPhotos)->delete();
                foreach ($removedPhotos as $photo) {
                    // Assuming 'Post/' is the storage folder, adjust it based on your configuration
                    Storage::delete($photo);
                }

            DB::commit();

            return response()->json([
                'message' => 'Post updated successfully',
                'data'=>$post
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $errorMessage = 'Post update failed: ' . $e->getMessage();
            Log::error($errorMessage);

            return response()->json(['error' => 'Failed to update post. Please try again.'], 500);
        }
    }


    public function delete($post)
    {
        try {
            DB::beginTransaction();

            // Get the post's photos
            $photos = $post->photos;

            // Delete the post
            $post->delete();

            // Delete the photos from the database
            $post->photos()->delete();

            // Delete the photos from storage
            foreach ($photos as $photo) {
                Storage::delete($photo->photo);
            }

            DB::commit();

            return response()->json([
                'message' => 'Post and its photos deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $errorMessage = 'Post deletion failed: ' . $e->getMessage();
            Log::error($errorMessage);

            return response()->json(['error' => 'Failed to delete post. Please try again.'], 500);
        }
    }

    protected function storePhotos($request, $post)
    {
        foreach ($request->file('photos') as $photo) {
            $this->savePhoto($post, $photo);
        }
    }


    protected function savePhoto($post, $photo)
    {
        $postPhoto = new PostPhotos();
        $postPhoto->post_id = $post;
        $postPhoto->photo = $photo->storeAs('Post',$photo->getClientOriginalName(),'public');
        $postPhoto->save();
    }


}
