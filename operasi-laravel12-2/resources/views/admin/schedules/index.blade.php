@extends('layouts.admin')
@section('title','Jadwal Operasi')
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@7.1.0/index.global.min.css">
<style>
#calendar{min-height:680px}.fc .fc-button-primary{background:#07558e;border-color:#07558e}.fc .fc-event{cursor:grab}.fc .fc-event:active{cursor:grabbing}
.conflict-note{background:#fff8e1;border-left:4px solid #ffc107;padding:.8rem 1rem;border-radius:.5rem}
</style>
@endpush
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
  <div><h4 class="mb-0">Jadwal Operasi</h4><small class="text-muted">Kalender dapat di-drag & drop. Sistem otomatis menolak bentrok OK, operator, anestesi, dan tim bedah.</small></div>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-secondary" target="_blank" href="{{ route('admin.schedules.print',['date'=>$date]) }}"><i class="bi bi-file-earmark-pdf"></i> Cetak / PDF</a>
    @if(auth()->user()->canAccess('schedule.manage'))<a class="btn btn-rs" href="{{ route('admin.schedules.create',['date'=>$date]) }}"><i class="bi bi-plus-lg"></i> Jadwal Baru</a>@endif
  </div>
</div>
<div class="conflict-note mb-3"><i class="bi bi-shield-check me-1"></i><b>Validasi bentrok aktif.</b> Durasi default operasi adalah 90 menit. Jadwal tidak dapat dipindahkan ke waktu yang berbenturan pada OK yang sama atau dengan operator/anestesi/tim bedah yang sama.</div>
<div class="card p-3 mb-4"><div id="calendar"></div></div>
<div class="card p-3"><form class="row g-2 align-items-end mb-3"><div class="col-md-3"><label class="form-label">Tanggal</label><input type="date" name="date" value="{{ $date }}" class="form-control"></div><div class="col-md-2"><button class="btn btn-outline-primary w-100">Tampilkan</button></div></form><div class="table-responsive"><table class="table table-hover align-middle"><thead><tr><th>Jam</th><th>OK</th><th>Pasien</th><th>JK</th><th>Umur</th><th>Diagnosa</th><th>Tindakan</th><th>Operator</th><th>Anestesi</th><th>Cito</th><th></th></tr></thead><tbody>@forelse($schedules as $s)<tr><td class="fw-bold">{{ substr($s->operation_time,0,5) }}</td><td><span class="badge {{ $s->ok_level==='OK 2'?'bg-success':($s->ok_level==='OK 3'?'bg-purple':'bg-primary') }}">{{ $s->ok_level }}</span></td><td>{{ $s->patient->name }}</td><td>{{ $s->patient->gender }}</td><td>{{ $s->patient->age }} th</td><td>{{ $s->diagnosis }}</td><td>{{ $s->procedure }}</td><td>{{ $s->operator?->name }}</td><td>{{ $s->anesthesia?->name }}</td><td>@if($s->cito)<span class="badge bg-danger">CITO</span>@endif</td><td class="text-nowrap">@if(auth()->user()->canAccess('schedule.manage'))<a class="btn btn-sm btn-outline-primary" href="{{ route('admin.schedules.edit',$s) }}">Edit</a><form class="d-inline" method="POST" action="{{ route('admin.schedules.destroy',$s) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus jadwal?')">Hapus</button></form>@endif</td></tr>@empty<tr><td colspan="11" class="text-center py-4 text-muted">Tidak ada jadwal.</td></tr>@endforelse</tbody></table></div></div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@7.1.0/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded',()=>{
 const el=document.getElementById('calendar');
 const csrf='{{ csrf_token() }}';
 const canEdit=@json(auth()->user()->canAccess('schedule.manage'));
 const cal=new FullCalendar.Calendar(el,{locale:'id',initialView:'dayGridMonth',initialDate:'{{ $date }}',height:'auto',editable:canEdit,eventDurationEditable:false,headerToolbar:{left:'prev,next today',center:'title',right:'dayGridMonth,timeGridWeek,listWeek'},events:'{{ route('admin.schedules.calendar.events') }}',eventClick:info=>{info.jsEvent.preventDefault();if(info.event.url)location.href=info.event.url;},dateClick:info=>{if(canEdit)location.href='{{ route('admin.schedules.create') }}?date='+info.dateStr;},eventDrop:async info=>{
   const d=info.event.start; const pad=n=>String(n).padStart(2,'0'); const date=d.getFullYear()+'-'+pad(d.getMonth()+1)+'-'+pad(d.getDate()); const time=pad(d.getHours())+':'+pad(d.getMinutes());
   try{const r=await fetch('{{ url('/admin/schedules') }}/'+info.event.id+'/move',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'},body:JSON.stringify({operation_date:date,operation_time:time})}); const data=await r.json(); if(!r.ok) throw new Error(data.message||Object.values(data.errors||{}).flat().join('\n')||'Bentrok jadwal'); alert(data.message||'Jadwal berhasil dipindahkan.'); location.reload();}catch(e){alert(e.message);info.revert();}
 }});cal.render();
});
</script>
@endpush
