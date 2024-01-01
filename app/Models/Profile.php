<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable=['user_id','first_name','last_name','birthday','gender','bio','profile_picture','contact_details'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    protected $casts=[
        'full_name'=>'string'
    ];
    public function getFullNameAttribute()
    {
        return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
    }


}
