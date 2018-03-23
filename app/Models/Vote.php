<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $table='vote';   

    protected $fillable=['mid','bestmid','teamid','matchlogid'];
}
