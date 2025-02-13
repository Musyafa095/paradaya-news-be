<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Otpcode;
use Carbon\Carbon;
use App\Mail\UserRegisterMail;
use App\Mail\GenerateEmailMail;
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

        return response()->json([
            'message' => 'User berhasil register, silahkan cek email anda',
            'user' => $user
        ], 201);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function login (Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ],[
            'required' => 'inputan :attribute wajib diisi',
        ]);

        $credentials = $request->only(['email', 'password']);
        if (!$token = auth()->attempt($credentials)){
            return response()->json(['error', 'Invalid User'], 401);
        }
        $user = User::where('email', $request->input('email'))->with(['profile' => function($query) {
            $query->select('user_id', 'age', 'bio');
        }, 'role'=> function($query){
            $query->select('id','name');
        },  'comments' => function($query) {
            $query->select('id', 'user_id', 'news_id', 'comment', 'created_at');
        },
        'comments.news' => function($query) {
            $query->select('id', 'title', 'content');
        }])->first();

        return response()->json([
            'message'=> 'User berhasil login',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function currentuser()
    {
            $user = auth()->user();
            $userData = User::with(['profile' => function($query) {
                $query->select('user_id', 'age', 'bio');
            }, 'role'=> function($query){
                $query->select('id','name');
            },  'comments' => function($query) {
                $query->select('id', 'user_id', 'news_id', 'comment', 'created_at');
            },
            'comments.news' => function($query) {
                $query->select('id', 'title', 'content');
            }])->find($user->id);
        return response()->json([
            'user' => $userData
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function logout()
    {
        auth()->logout();
        return response()->json([
            'message' => 'User berhasil logout'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function generateOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
          ],[
            'required' => 'inputan :attribute wajib diisi',
            'email' => 'inputan :attribute harus berformat email'
          ]);
          $user = User::where('email', $request->input('email'))->first();
          $user->generate_otp();
        
          Mail::to($user->email)->send(new GenerateEmailMail($user));
          return response()->json([
            'message' => 'OTP berhasil di generate, silahkan cek email anda'
          ]);
    }

    public function verifikasi(Request $request)
    {
        $request->validate([
            'otp' => 'required|min:6',
        ],[
            'required' => 'inputan :attribute wajib diisi',
            'min' => 'inputan maksimal :min karakter '
        ]);
           $user = auth()->user();
           //Jika otp tidak ditemukan
           $otp_code = Otpcode::where('otp', $request->input('otp'))->where('user_id', $user->id)->first();
           if (!$otp_code){
            return response()->json([
                'message' => 'OTP anda tidak ditemukan'
            ], 404);
           }
            //if OTP expired
            $now = Carbon::now();
            if ($now > $otp_code->valid_until){
                return response()->json([
                    'message' => 'OTP anda sudah kadaluarsa, silahkan generate ulang OTP anda'
                ], 400);
            }
            //update user
            $user =User::find($otp_code->user_id);
            $user->email_verified_at = $now;
            $user->save();
            $otp_code->delete();
            return response()->json([
              'message' => 'Verifikasi anda berhasil'
            ]);
    }
}
