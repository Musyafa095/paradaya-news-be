<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Otpcode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
           'name' => 'required|min:2',
           'email' => 'required|email|unique:users,id',
           'password' => 'required|min:8|confirmed'
        ],[
            'required' => 'inputan :attribute wajib diisi',
            'min' => 'inputan :attribute minimal :min karakter',
            'email' => 'inputan :attribute harus berformat email',
            'unique' => 'inputan :attribute sudah terdaftar',
            'confirmed' => 'inputan password tidak sama dengan confirmation password',
        ]);

        $user = new User;
        $roleUser = Role::where('name', 'user')->first();

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->role_id = $roleUser->id;
        $user->save();
        Mail::to($user->email)->send(new UserRegisterMail($user));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function login (Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function currentuser(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function logout(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function generateOtp(string $id)
    {
        //
    }

    public function verifikasi(string $id)
    {
        //
    }
}
