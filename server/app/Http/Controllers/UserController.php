<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username_email' => 'required|string',
            'password' => 'required|string',
        ]);
        $loginBy = filter_var($request->username_email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [$loginBy => $request->username_email, 'password' => $request->password];
        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
                'status' => 'success',
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
        ]);

    }

    public function register(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            //'profile_picture' => $request->profile_picture ?? 'profile_picture/default.jpg',
        ]);

        /*$user = User::create([
            'name' => $validatedData['name'],
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);*/

        $token = Auth::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

    public function getUserData()
    {
        $user = Auth::user();

        if(!$user){
            return response()->json([               
                'status'=> 'failed',
                'message'=> 'Not authenticated'
            ], 401);
        }

        $posts = $user->posts;
        return response()->json([
            'user' => $user,
            'posts' => $posts,
        ]);
    }

    public function editProfile(Request $request)
    {
        $user = Auth::user();

        if(!$user){
            return response()->json([               
                'status'=> 'failed',
                'message'=> 'Not authenticated'
            ], 401);
        }

        $request->validate([
            'name' => 'nullable|string|max:255',
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'bio' => 'nullable|string',
            'profile_picture' => 'image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if($request->has('name')){
            $user->name = $request->name;
        }

        if($request->has('username')){
            $user->username = $request->username;
        }

        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $file->move(public_path('profile_picture/'), $filename);
            $user->profile_picture = $filename;
        }

        if ($request->has('bio')) {
            $user->bio = $request->bio;
        }

        $user->save();
        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
        ]);
    }
}
