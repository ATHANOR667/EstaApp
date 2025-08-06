<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class SuperAdmin extends Authenticatable
{
    use HasFactory  , Notifiable , SoftDeletes ;



    protected $fillable = [
        'email',
        'password',
        'password_changed_at',
    ];

    protected $hidden = [
        'password',
        'password_changed_at',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    protected array $dates = [
        'password_changed_at' ,
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'password_changed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function booted() : void
    {
        static::creating(function ($superAdmin) {
            if (SuperAdmin::count() >= 1) {
                throw new \Exception('Impossible de créer plus d’un SuperAdmin.');
            }

           /* if (empty($superAdmin->id)) {
                $superAdmin->id = Str::uuid();
            }*/

        });
    }



}
