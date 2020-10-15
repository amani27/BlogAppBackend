<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    //
    protected $fillable = [
        'title', 'content', 'user_id', 'category_id'
    ];

    public function tags()
    {
        return $this->belongsToMany('App\Tag');
    }
}
