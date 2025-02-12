<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\News;

class CommentController extends Controller
{
    public function storeupdate (Request $request) {
        
        $request->validate([
            'comment' => 'required|min:10',
            
        ], [
            'required' => 'inputan :attribute wajib diisi',
            'comment.min' => 'inputan :attribute minimal 10 karakter'
        ]);
        $user = auth()->user();
        $news = News::find($request->input('movie_id'));
        if (!$news) {
            return response()->json([
                'message' => 'News not found'
            ], 404);
        }
        $comment = Comment::updateOrCreate(
            ['user_id' => $user->id, 'news_id' => $news -> id]
            ,[
                'comment' => $request->input('comment')
            ]);
            return response()->json([
                'message' => 'comment berhasil dibuat/diupdate',
                'data' => $comment,
            ], 201);
            
}
}
