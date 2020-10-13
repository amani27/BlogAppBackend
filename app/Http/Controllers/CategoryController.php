<?php

namespace App\Http\Controllers;


use DB;
use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //
    //////////////// get Categories list function
    public function getCategories()
    {
        $categories = DB::table('categories')->get();
        return response()->json($categories);
    }
}
