<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    //
    protected $fillable = [
        'title', 'content', 'user_id', 'category_id', 'average_rating', 'rating_count'
    ];

    public function categories()
    {
        return $this->belongsTo('App\Category', 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Tag');
    }

    public function blog_images()
    {
        return $this->hasMany('App\BlogImage');
    }

    public function ratings()
    {
        return $this->hasMany('App\Rating');
    }
  
    // public function averageRatings()
    // {
    //     return $this->hasOne('App\Rating')->;
    // }

    public function scopeWithFilters($query)
    {

        $tagNames = explode(',', request()->input('categories'));
        // // return $tagNames;
        // return $query->when(request()->input('tags'), function ($query) {
        //     // $tag_id  = $request->tag_id;
        //     // $tag = Tag::where('id', $tag_id)->get()->first();
        //     // $blogs = $tag->blogs;
        //     $query->whereHas('tags', function ($q) {
        //         $q->whereIn('tag_id', request()->input('tags'));
        //     });
        // })
        //     ->when(request()->input('categories'), function ($query) use ($tagNames) {
                $query->whereIn('category_id', $tagNames);
            // })
            // ->when(request()->input('rating'), function ($query) {
            //     if (request()->input('rating') == 'ASC') {
            //         $query->orderBy('average_rating', 'ASC');
            //     } else {
            //         $query->orderBy('average_rating', 'DESC');
            //     }
            // });
    }
}
