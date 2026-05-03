<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{

    public function index()
    {
        return view('auth.change_password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:6|confirmed'
        ]);

        $user = Auth::user();

        if(!Hash::check($request->password_lama, $user->password)){
            return back()->with('error','Password lama salah');
        }

        $user->update([
            'password' => Hash::make($request->password_baru)
        ]);

        return back()->with('success','Password berhasil diganti');
    }
}