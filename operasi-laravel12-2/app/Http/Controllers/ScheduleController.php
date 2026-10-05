<?php
namespace App\Http\Controllers;

use App\Models\{SurgerySchedule, Patient, Operator, Anesthesia, SurgicalTeam};
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', now('Asia/Jakarta')->toDateString());
        $schedules = SurgerySchedule::with(['patient','operator','anesthesia','surgicalTeam'])
            ->whereDate('operation_date',$date)->orderBy('operation_time')->get();
        return view('admin.schedules.index', compact('schedules','date'));
    }

    public function create(){ return $this->form(new SurgerySchedule()); }

    public function store(Request $r)
    {
        $data = $this->validateData($r);
        $this->assertNoConflict($data);
        $schedule = SurgerySchedule::create($data);
        return redirect()->route('admin.schedules.index',['date'=>$schedule->operation_date->toDateString()])->with('success','Jadwal operasi berhasil ditambahkan.');
    }

    public function edit(SurgerySchedule $schedule){ return $this->form($schedule); }

    public function update(Request $r, SurgerySchedule $schedule)
    {
        $data = $this->validateData($r);
        $this->assertNoConflict($data, $schedule->id);
        $schedule->update($data);
        return redirect()->route('admin.schedules.index',['date'=>$schedule->operation_date->toDateString()])->with('success','Jadwal operasi diperbarui.');
    }

    public function destroy(SurgerySchedule $schedule)
    {
        $date = $schedule->operation_date->toDateString();
        $schedule->delete();
        return redirect()->route('admin.schedules.index',['date'=>$date])->with('success','Jadwal dihapus.');
    }

    public function calendarEvents(Request $r)
    {
        $query = SurgerySchedule::with('patient')
            ->when($r->start,fn($q)=>$q->whereDate('operation_date','>=',substr($r->start,0,10)))
            ->when($r->end,fn($q)=>$q->whereDate('operation_date','<=',substr($r->end,0,10)))
            ->get();
        return $query->map(fn($s)=>[
            'id'=>$s->id,
            'title'=>$s->operation_time.' • '.$s->patient->name.' • '.$s->ok_level,
            'start'=>$s->start_date_time,
            'end'=>$s->end_date_time,
            'url'=>route('admin.schedules.edit',$s),
            'editable'=>auth()->user()->canAccess('schedule.manage'),
            'backgroundColor'=>$s->cito?'#dc3545':match($s->ok_level){'OK 2'=>'#16a34a','OK 3'=>'#9333ea',default=>'#0d6efd'},
            'borderColor'=>$s->cito?'#b02a37':match($s->ok_level){'OK 2'=>'#15803d','OK 3'=>'#7e22ce',default=>'#0b5ed7'},
            'extendedProps'=>['diagnosis'=>$s->diagnosis,'procedure'=>$s->procedure,'ok'=>$s->ok_level,'cito'=>$s->cito]
        ])->values();
    }

    public function move(Request $r, SurgerySchedule $schedule)
    {
        abort_unless(auth()->user()->canAccess('schedule.manage'), 403);
        $data = $r->validate(['operation_date'=>'required|date','operation_time'=>'required|date_format:H:i']);
        $payload = $schedule->toArray();
        $payload['operation_date'] = $data['operation_date'];
        $payload['operation_time'] = $data['operation_time'];
        $this->assertNoConflict($payload, $schedule->id);
        $schedule->update($data);
        return response()->json(['ok'=>true,'message'=>'Jadwal berhasil dipindahkan.']);
    }

    public function print(Request $request)
    {
        $date = $request->input('date', now('Asia/Jakarta')->toDateString());
        $schedules = SurgerySchedule::with(['patient','operator','anesthesia','surgicalTeam'])->whereDate('operation_date',$date)->orderBy('operation_time')->get();
        return view('admin.schedules.print', compact('schedules','date'));
    }

    private function form(SurgerySchedule $schedule)
    {
        return view('admin.schedules.form', compact('schedule') + [
            'patients'=>Patient::orderBy('name')->get(),
            'operators'=>Operator::where('active',true)->orderBy('name')->get(),
            'anesthesias'=>Anesthesia::where('active',true)->orderBy('name')->get(),
            'teams'=>SurgicalTeam::where('active',true)->orderBy('name')->get()
        ]);
    }

    private function validateData(Request $r): array
    {
        return $r->validate([
            'patient_id'=>'required|exists:patients,id','operation_date'=>'required|date','operation_time'=>'required|date_format:H:i',
            'operator_id'=>'nullable|exists:operators,id','anesthesia_id'=>'nullable|exists:anesthesias,id','surgical_team_id'=>'nullable|exists:surgical_teams,id',
            'ok_level'=>'required|in:OK 1,OK 2,OK 3','diagnosis'=>'nullable|string','procedure'=>'required|string|max:255',
            'cito'=>'nullable|boolean','status'=>'required|in:Terjadwal,Selesai,Batal','notes'=>'nullable|string'
        ]) + ['cito'=>$r->boolean('cito')];
    }

    private function assertNoConflict(array $data, ?int $ignoreId=null): void
    {
        $start = Carbon::parse($data['operation_date'].' '.$data['operation_time']);
        $end = $start->copy()->addMinutes(90);
        $query = SurgerySchedule::query()
            ->whereDate('operation_date',$data['operation_date'])
            ->when($ignoreId,fn($q)=>$q->where('id','!=',$ignoreId));
        $conflicts = $query->get()->filter(function($s) use ($data,$start,$end){
            if ($s->status === 'Batal') return false;
            $sStart = Carbon::parse($s->operation_date->format('Y-m-d').' '.$s->operation_time);
            $sEnd = $sStart->copy()->addMinutes(90);
            $overlap = $start->lt($sEnd) && $end->gt($sStart);
            if (!$overlap) return false;
            return ($data['ok_level'] === $s->ok_level)
                || ($data['operator_id'] && $s->operator_id && (int)$data['operator_id']===(int)$s->operator_id)
                || ($data['anesthesia_id'] && $s->anesthesia_id && (int)$data['anesthesia_id']===(int)$s->anesthesia_id)
                || ($data['surgical_team_id'] && $s->surgical_team_id && (int)$data['surgical_team_id']===(int)$s->surgical_team_id);
        });
        if ($conflicts->isNotEmpty()) {
            $c=$conflicts->first();
            $reason=[];
            if ($data['ok_level']===$c->ok_level) $reason[]='ruang '.$c->ok_level;
            if ($data['operator_id'] && $c->operator_id && (int)$data['operator_id']===(int)$c->operator_id) $reason[]='operator';
            if ($data['anesthesia_id'] && $c->anesthesia_id && (int)$data['anesthesia_id']===(int)$c->anesthesia_id) $reason[]='anestesi';
            if ($data['surgical_team_id'] && $c->surgical_team_id && (int)$data['surgical_team_id']===(int)$c->surgical_team_id) $reason[]='tim bedah';
            throw ValidationException::withMessages(['operation_time'=>'Bentrok jadwal dengan '.$c->patient->name.' pada '.substr($c->operation_time,0,5).' ('.implode(', ',$reason).'). Silakan pilih waktu/OK/petugas lain.']);
        }
    }
}
