<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SurgicalTeam extends Model { protected $fillable=['name','members','active']; protected function casts(): array{return ['members'=>'array','active'=>'boolean'];} public function schedules(){return $this->hasMany(SurgerySchedule::class);} }
