<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\User;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function addPost(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'caption' => 'required|string',
            'image' => 'required|image',
        ]);

        $post = new Post;
        
        $post->caption = $request->caption;
        $post->user_id = $user->id;

        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();
        $filename = time() . '.' . $extension;
        $file->move(public_path('posts_images/'), $filename);
        $post->image = $filename;
        $post->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Post created successfully',
            'post' => $post,
        ], 201);
    }
}
