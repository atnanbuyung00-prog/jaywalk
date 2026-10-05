<?php
namespace App\Http\Controllers;
use App\Models\{SurgerySchedule,Shift,ShiftAssignment}; use Illuminate\Http\Request;
class DisplayController extends Controller {
 public function index(){return view('display.index');}
 public function events(Request $r){$date=$r->input('date',now('Asia/Jakarta')->toDateString());$rows=SurgerySchedule::with(['patient','operator','anesthesia','surgicalTeam'])->whereDate('operation_date',$date)->orderBy('operation_time')->get();$shifts=Shift::where('active',true)->get()->map(fn($s)=>['name'=>$s->name,'members'=>ShiftAssignment::where('shift_id',$s->id)->whereDate('shift_date',$date)->pluck('staff_name')->values()]);return response()->json(['date'=>$date,'schedules'=>$rows,'shifts'=>$shifts]);}
}
