<?php

namespace App\Http\Controllers;

use DB;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class TestController extends Controller
{
    //////////////// register user function
    public function createNewUser(Request $request)
    {
        // return $request->all();
        $password = bcrypt($request->password);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $password,
        ]);
        return $user;
    }


    //////////////// update user function
    public function updateUser(Request $request)
    {
        // return $request->all();

        $id  = $request->id;
        $updatedUser = User::where('id', $id)->update([
            'name' => $request->name,
            // 'email' => $request->email,
        ]);

        // is ($updatedUser==1) return User::where('id', $id);
        return $updatedUser;
    }

    //////////////// delete user function
    public function deleteUser(Request $request)
    {
        // return $request->all();

        $id  = $request->id;
        $deletedUser = User::where('id', $id)->delete();

        return $deletedUser;
    }

    //////////////// get user info by id function
    public function getUserInfo(Request $request)
    {
        $id  = $request->id;
        // $userInfo = User::where('id', $id);
        // $userInfo = DB::table('users')->where('id', $id);
        $userInfo = DB::table('users')->find($id);

        return json_encode($userInfo);
    }


    //////////////// get all users list function
    public function getUsers()
    {
        $users = DB::table('users')->get();

        // return json_encode($users);
        return response()->json($users);
    }

    ////////////////// upload single image method start
    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image:jpeg,png,jpg,gif,svg|max:2048' // validate image file input
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->messages()->first(),
                    'success' => 'false'
                ],
                500
            );
        }

        $img = $request->file('image')->hashName(); // set image file name
        // $path = request()->file('image')->store('uploads', 'public');
        request()->file('image')->move(public_path('/uploads'), $img); // move uploaded file 
        $imgPath = "uploads/$img"; // set uploaded file path to return

        return response()->json([
            'success' => 'true',
            'image' => $imgPath
        ], 200);
    }

    ////////////////// upload multiple image method start
    public function uploadMultipleImages(Request $request)
    {
        //     if (!$request->hasFile('images')) {
        //         return response()->json(['upload_file_not_found'], 400);
        //     }
        // $allowedfileExtension = ['jpg', 'png'];
        $files = $request->file('images');
        // $errors = [];

        $validator = Validator::make($request->all(), [
            'images.*' => 'required|image:jpeg,png,jpg,gif,svg|max:2048' // validate image file input
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => 'invalid_file_format',
                    // 'message' => $validator->messages()->first(),
                    'success' => 'false'
                ],
                500
            );
        }
        $images = array();
        foreach ($files as $file) {
            $extension = $file->getClientOriginalExtension();

            // $check = in_array($extension, $allowedfileExtension);

            // if ($check) {
            foreach ($request->images as $mediaFiles) {
                $media_ext = $mediaFiles->getClientOriginalName();
                $media_no_ext = pathinfo($media_ext, PATHINFO_FILENAME);
                $mFiles = $media_no_ext . '-' . uniqid() . '.' . $extension;
                array_push($images, $mFiles);
                $mediaFiles->move(public_path() . '/images/', $mFiles);
            }
            // } else {
            //     return response()->json(['success' => 'false', 'message' => 'invalid_file_format']);
            // }

            return response()->json(['success' => 'true', 'message' => 'files_uploaded', 'images' => $images], 200);
        }
    }
}
