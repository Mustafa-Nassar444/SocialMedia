<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\ProfileResource;
use App\Models\Profile;
use App\Models\User;
use App\Traits\UploadImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    use UploadImageTrait;
    //
    /**
     * Display the authenticated user's profile.
     *
     * @return ProfileResource
     */
    public function show(){
        $user=Auth::guard('api')->user()->id;
        $profile=Profile::where('user_id',$user)->first();

        return new ProfileResource($profile);

    }


    /**
     * Update the authenticated user's profile.
     *
     * @param \Illuminate\Http\Request $request
     * @return ProfileResource
     */
    public function update(ProfileUpdateRequest $request)
    {
        $user=User::find(Auth::guard('api')->user()->id);
        $profile=Profile::where('user_id',$user->id)->first();
        if (!$profile) {
            $profile = new Profile(['user_id' => $user->id]);
            $profile->save();
        }
        $data = $request->except('profile_picture','password','email');

        $old_img=$profile->profile_picture;

        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $this->uploadImage($request,'profile_picture','Profile');
            $data['profile_picture'] = $profilePicturePath;
        }

        if($request->has('password')) {
            $data['password'] = bcrypt(request()->password);
            $user->update(['password' => $data['password']]);
        }
        else if ($request->has('email'))
        {
            $user->update(['email'=>$request->email]);
        }


        $profile->update($data);


        if ($old_img && isset($data['profile_picture'])) {
            Storage::delete($old_img);
        }

        return new ProfileResource($profile);
    }

    public function destroy()
    {
        $user=Auth::guard('api')->user();

        $profile = Profile::where('user_id', $user->id)->first();
        $profile->delete();

        return response()->json(['message' => 'Profile deleted successfully']);
    }



}
