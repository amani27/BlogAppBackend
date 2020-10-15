<?php

namespace App\Http\Controllers;

use DB;
use App\Blog;
use App\Tag;
use Illuminate\Http\Request;
use Validator;


class BlogController extends Controller
{
    //
    //////////////// craete blog function
    public function createNewBlog(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'title' => 'required|min:5',
            'content' => 'required|min:10',
            'user_id' => 'required',
            'category_id' => 'required',
        ]);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors()->first(),
            ], 400);
        }

        $blog = Blog::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => $request->user_id,
            'category_id'  => $request->category_id,
        ]);

        //
        if ($blog) {
            $tagNames = explode(',', $request->get('tags'));
            $tagIds = [];
            foreach ($tagNames as $tagName) {
                // $tag = $blog->tags()->create(['name' => $tagName]);
                //Or to take care of avoiding duplication of Tag
                //you could substitute the above line as
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                if ($tag) {
                    $tagIds[] = $tag->id;
                }
            }
            $blog->tags()->sync($tagIds);
        }
        //
        $blog->tags;

        return response()->json([
            'success' => true,
            'post' =>  $blog
        ], 200);
    }


    //////////////// get blogs list function (with related tags)
    public function getBlogs()
    {
        $blogs = Blog::all();

        foreach ($blogs as $blog) {
            $blog->tags;
        }

        return response()->json($blogs);
    }


    //////////////// get blogs list function
    public function getBlogsByUserId(Request $request)
    {
        $user_id  = $request->user_id;
        $blogs = DB::table('blogs')->where('user_id', $user_id)->get();
        // $blogs = DB::table('blogs')->select(DB::raw('* WHERE user_id=$user_id'))->get();

        return response()->json($blogs);
    }


    //////////////// get blogs list by catergory function start
    public function getBlogsByCategories(Request $request)
    {
        $category_id  = $request->category_id;
        $blogs = DB::table('blogs')->where('category_id', $category_id)->get();

        return response()->json($blogs);
    }
    //////////////// get blogs list by catergory function end


    //////////////// edit blog function
    public function editBlog(Request $request)
    {
        $validation = Validator::make($request->all(), [
            // 'bail' -> To stop running validation rules on an attribute after the first validation failure
            // Rules will be validated in the order they are assigned
            'title' => 'bail|required|min:5',
            // 'title' => 'required|min:5',
            'content' => 'required|min:10',
            'category_id' => 'required',
        ]);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors()->first(),
            ], 400);
        }

        $isUpdatedBlog = Blog::where('id', $request->id)->update(array('title' => $request->title, 'content' => $request->content, 'category_id' => $request->category_id,));

        if (!$isUpdatedBlog) {
            return response()->json([
                'success' => false,
                'message' => "Something went wrong!",
            ], 400);
        }

        $blog =  DB::table('blogs')->where('id', $request->id)->limit(1)->get();

        $data =   [
            "id" => $blog[0]->id,
            "title" => $blog[0]->title,
            "content" => $blog[0]->content,
            "category_id" => $blog[0]->category_id,
        ];

        return response()->json([
            'success' => true,
            'post' =>  $data,
        ], 200);
    }


    //////////////// delete blog function
    public function deleteBlog(Request $request)
    {
        $id  = $request->id;
        $deletedBlog = Blog::destroy($id);

        if ($deletedBlog == 0) {
            return response()->json([
                'success' => false,
            ], 400);
        }

        return response()->json([
            'success' => true,
        ], 200);
    }


    ///////////// get blogs by tag function
    public function getBlogsByTag(Request $request)
    {
        $tag_id  = $request->tag_id;
        $tag = Tag::where('id', $tag_id)->get()->first();
        $blogs = $tag->blogs;

        return response()->json($blogs);
    }


    ///////////// get tags by blog function
    public function getTagsByBlog(Request $request)
    {
        $blog_id  = $request->blog_id;
        // $blog = DB::table('blogs')->where('id', $blog_id)->get()->first();
        // $blog = Blog::find($blog_id);
        $blog = Blog::where('id', $blog_id)->get()->first();
        $tags = $blog->tags;

        // $blog = Blog::first();
        // $tags = $blog->tags;

        return response()->json($tags);
        // return response()->json($blog);
    }
}
