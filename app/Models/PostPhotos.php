<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostPhotos extends Model
{
    use HasFactory;
    protected $fillable=['post_id','photo'];

    protected $hidden=['id','post_id','created_at','updated_at'];
}
