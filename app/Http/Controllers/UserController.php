<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{

    //////////////////// login method
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                $user = User::where('email',  $request->input('email'))->count();

                if ($user == 0) {
                    // user doesn't exist
                    return response()->json(['success' => false, 'message' => "User doesn't exist with this email!"], 400);
                }

                return response()->json(['success' => false, 'message' => "Password doesn't match!"], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['success' => false, 'message' => 'could_not_create_token'], 500);
        }

        // get the user 
        $user = JWTAuth::user();

        // return response()->json(compact('token', 'user')); 
        return response()->json([
            'success' => true,
            'user' =>  $user,
            'token' => $token,
        ], 200);
    }

    //////////////////// register method
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            // return response()->json($validator->errors()->toJson(), 400);
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        // return response()->json(compact('user', 'token'), 200);
        return response()->json([
            'success' => true,
            'user' =>  $user,
            'token' => $token,
        ], 200);
    }


    //////////////////// get user details method
    public function getAuthenticatedUser()
    {
        try {

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());
        }

        return response()->json(compact('user'));
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
