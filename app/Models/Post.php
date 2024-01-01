<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'content',
        'original_post_id',
    ];

    protected $hidden=['updated_at','original_post_id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function photos(){
        return $this->hasMany(PostPhotos::class);
    }
    public function originalPost()
    {
        return $this->belongsTo(Post::class, 'original_post_id');
    }



    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

}
