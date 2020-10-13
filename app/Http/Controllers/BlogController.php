<?php

namespace App\Http\Controllers;

use DB;
use App\Blog;
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
            // 'category' => 'required',
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
            // 'category_id'  => $request->input('category.id'), // no use of this here
            // 'category'  => [
            //     'id' =>  $request->category_id,
            //     'name' => $request->category_name,
            // ],
        ]);
        // return $blog;
        return response()->json([
            'success' => true,
            'post' =>  $blog
        ], 200);
    }

    //////////////// get blogs list function
    public function getBlogs()
    {
        $blogs = DB::table('blogs')->get();


        return response()->json($blogs);
    }

    // //////////////// get blogs list function
    // public function getBlogsByCategory(Request $request)
    // {
    //     $blogs = DB::table('blogs')->where('category_id', $request->category_id)->get();
    //     // $category = DB::table('categories')->where('id', $request->category_id)->get();
    //     $category = DB::table('categories')->where('id', $request->category_id)->pluck('name');
    //     // $catDetails = array();

    //     // foreach ($blogs as $b) {
    //     //     if (DB::table('categories')->where('id', $b->category_id)->get()) array_push($catDetails, $b);
    //     // }

    //     // return response()->json(['categoryName' => $category, $blogs]);
    //     return response()->json(['categoryName' => $category[0], 'blogs' => $blogs]);
    // }

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

        // if ($isUpdatedBlog) $blog = Blog::where('id', $request->id);

        if (!$isUpdatedBlog) {
            return response()->json([
                'success' => false,
                'message' => "Something went wrong!",
            ], 400);
        }

        $blog =  DB::table('blogs')->where('id', $request->id)->limit(1)->get();

        $data =   [
            "id" => $blog[0]->id,
            // "id" => $request->id,
            "title" => $blog[0]->title,
            "content" => $blog[0]->content,
            "category_id" => $blog[0]->category_id,
        ];

        // return response()->json($blog);
        return response()->json([
            'success' => true,
            'post' =>  $data,
        ], 200);
    }

    //////////////// delete blog function
    public function deleteBlog(Request $request)
    {
        $id  = $request->id;
        // return $id;
        // $deletedBlog = Blog::where('id', $id)->delete(); // or
        // $deletedBlog = DB::table('blogs')->delete($id); // or
        $deletedBlog = Blog::destroy($id);

        // return $deletedBlog;

        if ($deletedBlog == 0) {
            return response()->json([
                'success' => false,
            ], 400);
        }

        return response()->json([
            'success' => true,
        ], 200);
    }
}
