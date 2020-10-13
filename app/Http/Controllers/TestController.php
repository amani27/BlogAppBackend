<?php

namespace App\Http\Controllers;

use DB;
use App\User;
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
}
