<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\News;

class CommentController extends Controller
{
    public function updateCreateComment(Request $request) {
        $request->validate([
            'comment' => 'required|min:5',
            'news_id' => 'required|exists:news,id'
        ], [
            'comment.required' => 'Komentar wajib diisi',
            'comment.min' => 'Komentar minimal 5 karakter',
            'news_id.required' => 'ID berita wajib diisi',
            'news_id.exists' => 'Berita tidak ditemukan'
        ]);
    
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'message' => 'User tidak terautentikasi'
            ], 401);
        }
    
        $news = News::find($request->input('news_id'));
        if (!$news) {
            return response()->json([
                'message' => 'Berita tidak ditemukan'
            ], 404);
        }
    
        try {
            $comment = Comment::updateOrCreate(
                ['user_id' => $user->id, 'news_id' => $news->id],
                ['comment' => $request->input('comment')]
            );
    
            return response()->json([
                'message' => 'Komentar berhasil dibuat/diupdate',
                'data' => $comment,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan komentar',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
}
