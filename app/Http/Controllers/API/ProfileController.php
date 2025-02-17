<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use Illuminate\Support\Facades\Log; // Untuk logging error

class ProfileController extends Controller
{
    public function updateProfile(Request $request)
    {
        // Ambil user yang sedang login
        $user = auth()->user();

        // Validasi input
        $request->validate([
            'bio' => 'required|string',
            'age' => 'required|integer',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048', // Gambar opsional
        ], [
            'required' => 'Inputan :attribute wajib diisi',
            'integer' => 'Inputan :attribute harus berupa angka',
            'image' => 'File harus berupa gambar',
            'mimes' => 'File harus berformat: jpg, png, jpeg, gif, svg',
            'max' => 'Ukuran file tidak boleh lebih dari 2MB',
        ]);

        try {
            $profile = Profile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'bio' => $request->input('bio'),
                    'age' => $request->input('age'),
                ]
            );
            if ($request->hasFile('image')) {
                $uploadedFileUrl = cloudinary()->upload($request->file('image')->getRealPath(), [
                    'folder' => 'image',
                ])->getSecurePath();

                $profile->image = $uploadedFileUrl;
                $profile->save();
            }

            // Beri respons sukses
            return response()->json([
                'message' => 'Profil berhasil dibuat/diupdate',
                'data' => $profile,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengupdate profil',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}