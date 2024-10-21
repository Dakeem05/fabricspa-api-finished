<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/images/{image}', function ($post) {
    if(File::exists(public_path().'uploads/images/'.$post)){
        return response()->file(public_path().'uploads/images/'.$post);
    } else {
        
        return response('not found');
    }
});
