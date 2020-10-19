<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlogImage extends Model
{
    //
    protected $fillable = [
        'blog_id', 'blog_image_path'
    ];

    public function blog()
    {
        return $this->belongsTo('App\Blog');
    }
}
