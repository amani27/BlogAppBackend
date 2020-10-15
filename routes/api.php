<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/register', 'TestController@createNewUser');

Route::post('/updateUser/{id}', 'TestController@updateUser');

Route::post('/deleteUser/{id}', 'TestController@deleteUser');
// Route::delete('/deleteUser/{id}', 'TestController@deleteUser');
// Route::delete('/deleteUser/{id}', [ 'uses' => 'TestController@deleteUser']);

Route::get('/user/{id}', 'TestController@getUserInfo'); // to get user info by id
Route::get('/users', 'TestController@getUsers'); // to get all users list


// route grouping
Route::group(
    ['prefix' => 'blogs'], // prefixing all routes in this group with blogs
    function () {
        Route::post('/createNewBlog', 'BlogController@createNewBlog'); //create

        Route::get('/allBlogsList', 'BlogController@getBlogs'); // read
        Route::get('/allBlogsListByUserId/{user_id}', 'BlogController@getBlogsByUserId'); // read
        Route::get('/allBlogsByCategory/{category_id}', 'BlogController@getBlogsByCategories'); // read

        Route::get('/allTagsByBlog/{blog_id}', 'BlogController@getTagsByBlog'); // read
        Route::get('/allBlogsByTag/{tag_id}', 'BlogController@getBlogsByTag'); // read

        Route::post('/editBlog', 'BlogController@editBlog'); // update

        Route::post('/deleteBlog/{id}', 'BlogController@deleteBlog'); // delete

        Route::get('/allCategories', 'CategoryController@getCategories'); // read

        
    }
);

Route::get('/allTagsList', 'TagController@getTags');
