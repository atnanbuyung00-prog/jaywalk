<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $fillable = ['name','username','password','permissions'];
    protected $hidden = ['password','remember_token'];
    protected function casts(): array { return ['password'=>'hashed','permissions'=>'array']; }
    public function canAccess(string $permission): bool {
        return $this->username === 'admin' || in_array($permission, $this->permissions ?? [], true);
    }
}
