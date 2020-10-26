<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    //
    protected $fillable = [
        'title', 'content', 'user_id', 'category_id', 'average_rating', 'rating_count'
    ];

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

    public function scopeWithFilters($query)
    {
        return $query->when(request()->input('tags', []), function ($query) {
            // $tag_id  = $request->tag_id;
            // $tag = Tag::where('id', $tag_id)->get()->first();
            // $blogs = $tag->blogs;
            $query->whereHas('tags', function ($q) {
                $q->whereIn('tag_id', request()->input('tags'));
            });
        })
            ->when(count(request()->input('categories', [])), function ($query) {
                $query->whereIn('category_id', request()->input('categories'));
            })
            ->when(request()->input('rating'), function ($query) {
                if (request()->input('rating') == 'ASC') {
                    $query->orderBy('average_rating', 'ASC');
                } else {
                    $query->orderBy('average_rating', 'DESC');
                }
            });
    }
}
