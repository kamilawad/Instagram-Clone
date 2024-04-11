<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;

class LikeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function getPostLikes(Request $request)
    {
        $post = Post::find($request->post_id);
        $likes = $post->likes;

        return response()->json([
            'status' => 'success',
            'likes' => $likes,
        ]);
    }

    public function likePost(Request $request)
    {
        $user = Auth::user();
        $like = $user->likes()->where('post_id', $request->post_id)->first();

        /*$post = Post::find($request->post_id); if (!$post) {return response()->json(['status' => 'error','message' => 'Post not found',], 404);}
        $follow = $user->following()->where('following_id', $post->user_id)->exists();
        if (!$follow) {
            return response()->json([
                'status' => 'error',
                'message' => 'You must follow the user to like their post',
            ], 403);
        }
        $like = $post->likes()->where('user_id', $user->id)->first();*/

        if ($like) {
            $like->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Like removed',
            ], 200);
        } else {
            $like = new Like;
            $like->user_id = $user->id;
            $like->post_id = $request->post_id;
            $like->save();
            return response()->json([
                'status' => 'success',
                'message' => 'You have liked the post',
            ], 200);
        }
    }
}
