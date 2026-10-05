<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SurgerySchedule extends Model {
    protected $fillable=['patient_id','operator_id','anesthesia_id','surgical_team_id','operation_date','operation_time','ok_level','diagnosis','procedure','cito','status','notes'];
    protected function casts(): array { return ['operation_date'=>'date','cito'=>'boolean']; }
    public function patient(){return $this->belongsTo(Patient::class);}
    public function operator(){return $this->belongsTo(Operator::class);}
    public function anesthesia(){return $this->belongsTo(Anesthesia::class);}
    public function surgicalTeam(){return $this->belongsTo(SurgicalTeam::class);}
    public function getStartDateTimeAttribute(){ return $this->operation_date->format('Y-m-d').'T'.$this->operation_time; }
    public function getEndDateTimeAttribute(){ return $this->operation_date->format('Y-m-d').'T'.date('H:i', strtotime($this->operation_time.' + 90 minutes')); }
}
