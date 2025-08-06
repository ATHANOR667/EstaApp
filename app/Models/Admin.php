<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasFactory  , Notifiable , SoftDeletes, HasRoles ;


    protected $fillable = [
        'matricule',
        'nom',
        'prenom',
        'email',
        'password',
        'password_changed_at',
        'passcode',
        'telephone',
        'pays',
        'ville',
        'photoProfil',
        'pieceIdentiteRecto',
        'pieceIdentiteVerso',
        'passcode_reset_status',
        'passcode_reset_date'
    ];

    protected $hidden = [
        'password_changed_at' ,
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'password_changed_at'  => 'datetime' ,
        'passcode_reset_date'  => 'datetime' ,
        'created_at' => 'datetime' ,
        'updated_at' => 'datetime' ,
        'deleted_at' => 'datetime' ,
    ];


    protected static function boot(): void
    {
        parent::boot();


        static::creating(function ($admin) {
            if (empty($admin->matricule)) {
                $admin->matricule = Str::uuid();
            }

           /* if (empty($admin->id)) {
                $admin->id = Str::uuid();
            }*/
        });
    }

}
