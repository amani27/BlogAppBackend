<?php

namespace App\Http\Controllers;

use DB;
use App\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    //
    //////////////// get tags list function
    public function getTags()
    {
        $tags = DB::table('tags')->get();
        return response()->json($tags);
    }
}
