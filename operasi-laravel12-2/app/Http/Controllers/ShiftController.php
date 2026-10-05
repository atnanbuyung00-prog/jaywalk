<?php
namespace App\Http\Controllers;
use App\Models\{Shift,ShiftAssignment}; use Illuminate\Http\Request;
class ShiftController extends Controller {
 public function index(){ $shifts=Shift::with('assignments')->orderBy('id')->get(); return view('admin.shifts.index',compact('shifts')); }
 public function store(Request $r){$d=$r->validate(['name'=>'required','start_time'=>'required','end_time'=>'required','color'=>'nullable','active'=>'nullable|boolean']);Shift::create($d);return back()->with('success','Shift berhasil ditambahkan.');}
 public function storeAssignment(Request $r, Shift $shift){ $d=$r->validate(['staff_name'=>'required','staff_role'=>'nullable','shift_date'=>'required|date']); ShiftAssignment::create(['shift_id'=>$shift->id,'staff_name'=>$d['staff_name'],'staff_role'=>$d['staff_role']??'Dinas','shift_date'=>$d['shift_date']]); return back()->with('success','Petugas shift berhasil ditambahkan.'); }
 public function destroyAssignment(Shift $shift, ShiftAssignment $assignment){ abort_unless($assignment->shift_id===$shift->id,404); $assignment->delete(); return back()->with('success','Petugas shift dihapus.'); }
 public function update(Request $r,Shift $shift){$d=$r->validate(['name'=>'required','start_time'=>'required','end_time'=>'required','color'=>'nullable','active'=>'nullable|boolean']);$shift->update($d);return back()->with('success','Shift diperbarui.');}
 public function destroy(Shift $shift){$shift->delete();return back()->with('success','Shift dihapus.');}
}
