<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function getPostComments(Request $request)
    {
        $request->validate([
            'post_id' => 'required|integer',
        ]);
        $post = Post::find($request->post_id);
        if (!$post) {
            return response()->json([
                'status' => 'error',
                'message' => 'Post not found',
            ], 404);
        }
        $comments = $post->comments;

        return response()->json([
            'status' => 'success',
            'comments' => $comments,
        ]);
    }

    public function addComment(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'post_id' => 'required|integer',
            'comment' => 'required|string',
        ]);
        
        $post = Post::find($request->post_id);
        if (!$post) {
            return response()->json([
                'status' => 'error',
                'message' => 'Post not found',
            ], 404);
        }

        $follow = $user->following()->where('following_id', $post->user_id)->exists();
        if (!$follow) {
            return response()->json([
                'status' => 'error',
                'message' => 'You must follow the user to like their post',
            ], 403);
        }

        $comment = new Comment;
        $comment->user_id = $user->id;
        $comment->post_id = $request->post_id;
        $comment->comment = $request->comment;
        $comment->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Comment added successfully',
            'comment' => $comment,
        ], 201);
    }
}
