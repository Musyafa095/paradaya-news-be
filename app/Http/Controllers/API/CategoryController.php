<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
     public function __construct()
    {
        $this->middleware(['auth:api', 'admin'])->except(['index', 'show']);
    } 
    public function index()
    {
        $categories = Category::all();
        return response()->json([
            'message' => 'Berhasil menampilkan data category',
            'data' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
             'name' => 'required|min:2',
        ],[
            'name.required' => 'kolom name harus diisi',
            'name.min' => 'kolom name minimal 2 karakter'
        ]);
        Category::create([
             'name' => $request->input('name'),
        ]);
        return response()->json(['message' => 'Berhasil menambahkan data category'], 200);
    }

    public function show($id)

    {
       
        $category = Category::with('news')->find($id);
        
        return response()->json([
            'message' => 'Detail untuk data Category',
            'data' => $category
        ]);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        $request->validate([
            'name' => 'required|string',
        ]);
            if(!$category) {
                return response ()-> json([
                      'message' => 'Data category tidak di temukan',
                ], 404);

            }
        
        $category->update($request->all());
        return response()->json([
            'message' => 'Update data genre telah berhasil',
            'data' => $category
        ]);
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        $category -> delete();
       return response()->json([
        'message' => 'Berhasil menghapus data category'
       ]);
       
    }
}