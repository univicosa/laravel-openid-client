<?php

namespace Modules\OpenId\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $connection = NULL;

    public $incrementing = FALSE;

    protected $keyType = 'uuid';

    protected $fillable = [
        'id', 'name', 'roles', 'registries', 'cpf', 'email', 'avatar',
    ];
}
