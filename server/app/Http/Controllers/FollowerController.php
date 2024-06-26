<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Follower;
use App\Models\User;

class FollowerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function getFollow(Request $request)
    {
        $user = Auth::user();

        if(!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $followers = $user->followers;
        $following = $user->following;

        return response()->json([
            'message' => 'Found',
            'followedBy' => $followers,
            'following' => $following,
        ]);
    }

    public function getAllUsers()
    {
        $user = Auth::user();

        if(!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $allUsers = User::where('id', '!=', $user->id)->get();

        foreach ($allUsers as $userToFollow) {
            $userToFollow->following = $user->following()->where('following_id', $userToFollow->id)->exists();
        }

        return response()->json([
            'message' => 'Found',
            'users' => $allUsers,
        ]);
    }

    public function follow(Request $request)
    {
        //$folower = Auth::user();
        $follower = auth()->user();
        $following = User::find($request->following_id);
        if (!$following) {
            return response()->json([
                'message' => 'User Not Found',
            ], 404);
        }

        if($follower->following()->where('following_id', $request->following_id)->exists() || $follower->id == $request->following_id){
            return response()->json([
                'message' => 'You already followed this user',
            ]);
        }
        
        //$follower->following()->attach($following);
        Follower::create([
            'follower_id' => $follower->id,
            'following_id' => $request->following_id,
        ]);

        return response()->json(['message' => 'User followed']);
    }

    public function unfollow(Request $request)
    {
        $follower = Auth::user();
        
        if (!User::find($request->following_id)) {
            return response()->json([
                'message' => 'User Not Found',
            ], 404);
        }

        $follow = $follower->following()->where('following_id', $request->following_id);
        if(!$follow->exists() || $follower->id == $request->following_id){
            return response()->json([
                'message' => 'You already not following this user',
            ]);
        }
        //$follower->following()->detach($User::find($request->following_id);
        $follow ->delete();

        return response()->json(['message' => 'User unfollowed']);
    }
}
