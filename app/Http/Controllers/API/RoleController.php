<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;

class RoleController extends Controller
{
    public function __construct(){
        $this->middleware(['auth:api', 'admin']);
    }

public function index (){
    $roles = Role::all();
    return response()->json([
        'message' => 'Data role berhasil di tampilkan',
        'data' => $roles
    ], 200);
}
public function store(Request $request){
    $request->validate(['name' => 'required|string'], [
       'name.required' => 'Nama role harus diisi.',
       'name.min' => 'Nama role minimal terdiri dari 2 karakter.',
    ]);
    Role::create([
        'name' => $request->input('name'),
    ]);
    
    return response()->json([
        'message' => 'Berhasil menambahkan role'
    ], 201);
    

}
public function show ($id){
    $role = Role::with('user')->find($id);
    return response()->json([
        'message' => 'Detail data role',
        'data' => $role
    ], 200);
} 
public function update (Request $request, $id){
    $request->validate(['name' => 'required|string']);
    $role = Role::find($id);
    $role->update($request->only('name'));
    return response()->json(['message' => 'Berhasil Update role'], 200);
}
public function destroy($id){
    $role = Role::find($id);
    $role->delete();
    return response()->json([
        'message' => 'Berhasil menghapus role'
    ], 200);
}
    
}
