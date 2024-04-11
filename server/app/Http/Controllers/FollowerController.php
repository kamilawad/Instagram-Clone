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

        Follower::create([
            'follower_id' => $follower->id,
            'following_id' => $request->following_id,
        ]);

        return response()->json(['message' => 'User followed',$follower->following,$follower->followers]);
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

        $follow ->delete();

        return response()->json(['message' => 'User unfollowed',$follower->following,$follower->followers]);
    }
}
