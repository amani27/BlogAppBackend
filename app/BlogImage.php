<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlogImage extends Model
{
    //
    protected $fillable = [
        'blog_id', 'blog_image_path', 'blog_image_caption'
    ];

    public function blog()
    {
        return $this->belongsTo('App\Blog');
    }
}
