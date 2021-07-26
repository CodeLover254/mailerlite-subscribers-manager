<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiUser extends Model
{
    protected $table='api_users';
    protected $fillable=['api_key'];
    public $timestamps=false;
}
