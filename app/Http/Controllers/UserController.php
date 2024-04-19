<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;

class UserController extends Controller
{
    public function storeAvatar(Request $request) {
        $request->validate([
            'avatar' => 'required|image|max:10000'
        ]);

        $user= auth()->user();

        $filename = $user->id . "-" . uniqid() . ".jpg";

        $manager = new ImageManager(new Driver());
        $image = $manager->read($request->file('avatar'));
        $imgData = $image->cover(120, 120)->toJpeg();
        Storage::put("public/avatars/" . $filename, $imgData);

        $oldAvatar = $user->avatar;

        $user->avatar = $filename;
        $user->save();

        if ($oldAvatar != "/fall-avatar.jpg") {
            Storage::delete(str_replace("/storage/", "public/", $oldAvatar));
        }

        return back()->with('success', 'Avatar lookin good!');
    }

    public function showAvatarForm(){
        return view('avatar-form');
    }

    public function showCorrectHomepage() {
        if (auth()->check()) {
            return view('homepage-feed');
        } else {
            return view('homepage');
        }
    }

    public function logout() {
        auth()->logout();
        return redirect('/')->with('success', 'You are now logged out.');
    }

    public function profile(User $user) {
        return view('profile-posts', 
        ['avatar' => $user->avatar,
        'username' => $user->username, 
        'posts' => $user->posts()->latest()->get(), 
        'postCount' => $user->posts()->count()]);
    }

    public function register(Request $request) {

        $incomingFields = $request->validate([
            'username' => 'required|string|min:3|max:20|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed'
        ]);

        $user = User::create($incomingFields);
        auth()->login($user);
        return redirect('/')->with('success', 'Thank you for creating an account.');
    }

    public function login(Request $request) {
        $incomingFields = $request->validate ([
            'loginusername' => 'required',
            'loginpassword' => 'required'
        ]);

        if (auth()->attempt(['username' => $incomingFields['loginusername'], 'password' => $incomingFields['loginpassword']])) {
            $request->session()->regenerate();
            return redirect('/')->with('success', 'You have successfully logged in.');
        } else {
            return redirect('/')->with('failure', 'Invalid login');
        }
    }
}
