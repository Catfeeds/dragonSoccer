<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Balancelog extends Model{
    protected $table='balance_log';
    protected $fillable=['sn','mid','type','number'];
    
}