<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait UploadImageTrait
{
    public function uploadImage(Request $request,$name,$folder){
        $img=$request->file($name)->getClientOriginalName();
        $path=$request->file($name)->storeAs($folder,$img,'public');
        return $path;
    }
}
