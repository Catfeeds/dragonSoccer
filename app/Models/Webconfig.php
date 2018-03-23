<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Webconfig extends Model{

    protected $table='webconfig';

    protected $fillable=['key','val'];
}