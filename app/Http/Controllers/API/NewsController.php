<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;

class NewsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'admin'])->except(['index', 'show']);
    }
    public function index(Request $request)
    {
        $query = News::query();

        if ($request->has('search')) {
            $searching = $request->input('search');
            $query->where('name', "LIKE", "%$searching%");
        }

        $per_page = $request->input('per_page', 8);

        $allNews = $query->paginate($per_page);

        return response()->json([
            'message' => 'Berita berhasil diTampilkan semua.',
            'data' => $allNews
        ], 200);
        
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'image' => "required|image|mimes:jpg,png,jpeg,gif,svg|max:2048",
            'date' => 'required',
            'category_id' => 'required|exists:categories,id'
        ]);
        $uploadedFileUrl = cloudinary()->upload($request->file('image')->getRealPath(), [
            'folder' => 'image',
        ])->getSecurePath();
            $News = new News;
            $News->title = $request->input('title');
            $News ->content = $request->input('content');
            $News -> date = $request->input('date');
            $News -> category_id = $request->input('category_id');
            $News -> image = $uploadedFileUrl;
    
            $News->save();
            return response()->json([
                'message' => 'Berhasil menambahkan berita',
            ], 200);   
    }

    public function show($id)
    {
        $News = News::with('category', 'comment')->find($id);
        if (!$News){
            return response()->json([
                'message' => 'Data berita tidak ditemukan'
            ], 404);    
        }
        return response()->json([
            'message' => 'Detail berita berhasil ditampilkan',
            'data' => $News
        ], 200);
    }

    public function update(Request $request,  $id)
    {
        $request->validate([
            'image' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'title' => 'required',
            'content' => 'required',
            'date' => 'required',
            'category_id' => 'required|exists:categories,id'
        ]);

        $News = News::find($id);
        if ($request->hasFile('image')) {
            $uploadedFileUrl = cloudinary()->upload($request->file('image')->getRealPath(), [
                'folder' => 'image',
            ])->getSecurePath();
            $News -> image = $uploadedFileUrl;
        }
       
        if (!$News) {
            return response()->json([
                'message' => 'berita tidak ditemukan',
            ], 404);
        }
            $News->title = $request->input('title');
            $News -> content = $request->input('content');
            $News -> date = $request->input('date');
            $News -> category_id = $request->input('category_id');
      
    
            $News->save();
            return response()->json([
                'message' => 'Berhasil mengupdate berita',
            ], 200);
        
    }

    public function destroy($id)
    {
        $News = News::find($id);
        $News->delete();
        return response()->json([
            'message' => 'Berhasil menghapus berita'
        ], 200);
    }
}
