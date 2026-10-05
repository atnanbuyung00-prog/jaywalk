<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\{Operator,Anesthesia,SurgicalTeam,Patient};
class MasterDataController extends Controller {
 private array $map=['operators'=>[Operator::class,'Operator'],'anesthesias'=>[Anesthesia::class,'Anestesi'],'teams'=>[SurgicalTeam::class,'Tim Bedah'],'patients'=>[Patient::class,'Pasien']];
 public function index(string $type){abort_unless(isset($this->map[$type]),404);[$class,$label]=$this->map[$type];$items=$class::orderBy('name')->get();return view('admin.masters.index',compact('items','type','label'));}
 public function store(Request $r,string $type){abort_unless(isset($this->map[$type]),404);[$class]= $this->map[$type];$data=$this->validateType($r,$type); if($type==='teams' && isset($data['members'])) $data['members']=array_values(array_filter(array_map('trim',explode(',',(string)$data['members'])))); $class::create($data); return back()->with('success','Data berhasil ditambahkan.');}
 public function update(Request $r,string $type,$item){abort_unless(isset($this->map[$type]),404);[$class]= $this->map[$type];$model=$class::findOrFail($item); $data=$this->validateType($r,$type); if($type==='teams' && isset($data['members'])) $data['members']=array_values(array_filter(array_map('trim',explode(',',(string)$data['members'])))); $model->update($data);return back()->with('success','Data berhasil diperbarui.');}
 public function destroy(string $type,$item){abort_unless(isset($this->map[$type]),404);[$class]= $this->map[$type];$class::findOrFail($item)->delete();return back()->with('success','Data berhasil dihapus.');}
 private function validateType(Request $r,string $type):array{if($type==='patients')return $r->validate(['name'=>'required','medical_record_no'=>'nullable','gender'=>'required|in:P,L','birth_date'=>'required|date']); if($type==='teams')return $r->validate(['name'=>'required','members'=>'nullable']); return $r->validate(['name'=>'required','license_no'=>'nullable','specialty'=>'nullable','active'=>'nullable|boolean']);}
}
