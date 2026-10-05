<?php
namespace App\Http\Controllers;
use App\Models\SurgerySchedule; use App\Models\Patient; use App\Models\Operator; use App\Models\Anesthesia; use App\Models\SurgicalTeam;
class DashboardController extends Controller { public function index(){ $today=now('Asia/Jakarta')->toDateString(); return view('admin.dashboard',compact('today')+['todayCount'=>SurgerySchedule::whereDate('operation_date',$today)->count(),'patientCount'=>Patient::count(),'operatorCount'=>Operator::where('active',true)->count(),'anesthesiaCount'=>Anesthesia::where('active',true)->count(),'teamCount'=>SurgicalTeam::where('active',true)->count()]); } }
