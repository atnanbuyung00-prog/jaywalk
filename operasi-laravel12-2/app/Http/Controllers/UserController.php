<?php
namespace App\Http\Controllers;
use App\Models\User; use Illuminate\Http\Request; use Illuminate\Support\Facades\Hash;
class UserController extends Controller {
 public const PERMISSIONS=['schedule.view'=>'Lihat jadwal','schedule.manage'=>'Kelola jadwal','master.manage'=>'Kelola data master','shift.manage'=>'Kelola shift','user.manage'=>'Kelola user'];
 public function index(){return view('admin.users.index',['users'=>User::orderBy('name')->get(),'permissions'=>self::PERMISSIONS]);}
 public function store(Request $r){$d=$r->validate(['name'=>'required','username'=>'required|unique:users,username','password'=>'required|min:4','permissions'=>'array']);User::create(['name'=>$d['name'],'username'=>$d['username'],'password'=>Hash::make($d['password']),'permissions'=>$d['permissions']??[]]);return back()->with('success','User berhasil dibuat.');}
 public function update(Request $r,User $user){$d=$r->validate(['name'=>'required','username'=>'required|unique:users,username,'.$user->id,'password'=>'nullable|min:4','permissions'=>'array']);$user->name=$d['name'];$user->username=$d['username'];$user->permissions=$d['permissions']??[];if(!empty($d['password']))$user->password=Hash::make($d['password']);$user->save();return back()->with('success','User diperbarui.');}
 public function destroy(User $user){abort_if($user->username==='admin',403,'Admin utama tidak boleh dihapus.');$user->delete();return back()->with('success','User dihapus.');}
}
