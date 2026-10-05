<?php
namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
class Patient extends Model {
    protected $fillable=['medical_record_no','name','gender','birth_date'];
    protected $appends = ['age'];
    protected function casts(): array { return ['birth_date'=>'date']; }
    public function getAgeAttribute(): int { return $this->birth_date ? $this->birth_date->age : 0; }
    public function schedules(){ return $this->hasMany(SurgerySchedule::class); }
}
